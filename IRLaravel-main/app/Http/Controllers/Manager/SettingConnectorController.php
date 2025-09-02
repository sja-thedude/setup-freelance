<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateSettingConnectorRequest;
use App\Http\Requests\UpdateSettingConnectorRequest;
use App\Models\SettingConnector;
use App\Repositories\SettingConnectorRepository;
use App\Services\Connector\HendrickxKassaConnector;
use Flash;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Log;
use Response;

class SettingConnectorController extends BaseController
{
    /** @var  SettingConnectorRepository */
    private $settingConnectorRepository;

    /**
     * SettingConnectorController constructor.
     * @param SettingConnectorRepository $settingConnectorRepository
     */
    public function __construct(SettingConnectorRepository $settingConnectorRepository)
    {
        parent::__construct();

        $this->settingConnectorRepository = $settingConnectorRepository;
    }

    /**
     * Show manager table
     *
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $connectors = $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, $this->perPage, $request->all());

        return view($this->guard.'.settings.connectors.index')
            ->with(compact('connectors'));
    }

    /**
     * Show test results..
     *
     * @param Request $request
     * @param int $workspaceId
     * @param int $id
     * @return void
     */
    public function test(Request $request, int $id)
    {
        $settingConnector = $this->settingConnectorRepository->findWithoutFail($id);

        if (empty($settingConnector)) {
            Flash::error(trans('setting.connectors.manager.not_found'));
            return redirect(route($this->guard.'.settings.connector.index'));
        }

        return view($this->guard.'.settings.connectors.test')
            ->with(compact('settingConnector'));
    }

    /**
     * Do test API call with our kassa service..
     *
     * @param Request $request
     * @param int $workspace
     * @param int $id
     * @return void
     */
    public function testAjax(
        Request $request,
        int $id
    ) {
        /** @var SettingConnector $settingConnector */
        $settingConnector = $this->settingConnectorRepository->findWithoutFail($id);

        // Currently we only support connector with provider "PROVIDER_HENDRICKX_KASSAS"
        if (empty($settingConnector) || $settingConnector->provider != SettingConnector::PROVIDER_HENDRICKX_KASSAS) {
            Flash::error(trans('setting.connectors.manager.not_found'));
            return redirect(route($this->guard.'.settings.connector.index'));
        }

        // Returned data
        $data = [];

        // Init API service
        $hendrickxKassaConnector = new HendrickxKassaConnector($settingConnector);

        // Switch between possible API calls.
        $action = $request->post('action');
        switch($action) {
            case 'test':
                $data = $hendrickxKassaConnector->doTest();
                break;

            case 'payment-methods':
                $data = $hendrickxKassaConnector->getPaymentTypes();
                break;

            case 'products':
                ini_set('memory_limit','1024M');
                $data = $hendrickxKassaConnector->getAllProducts();
                break;
        }

        return response()->json([
            'code' => 200,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new connector.
     *
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return response()->json([
            'code' => 200,
            'data' => view('manager.settings.connectors.partials.form', [
                'action'      => route($this->guard . '.settings.connector.store'),
                'method'      => 'POST',
                'idForm'      => 'create_option',
                'titleModal'  => trans('setting.connectors.manager.add_connector'),
                'settingConnector' => new SettingConnector(),
                'workspaceId' => $this->tmpWorkspace->id
            ])->render(),
        ]);
    }

    /**
     * Store a newly created connector
     *
     * @param CreateSettingConnectorRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingConnectorRequest $request)
    {
        try {
            \DB::beginTransaction();

            $input = $request->all();
            $input['workspace_id'] = $this->tmpWorkspace->id;

            if($input['provider'] == SettingConnector::PROVIDER_CUSTOM) {
                $secretKey = sha1(time());
                $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secretKey));
                $now = new \DateTimeImmutable();
                $tokenObj = $config->builder()
                    ->expiresAt($now->modify('+'. config('jwt.connector.ttl') .' minutes'))
                    ->withClaim('secret', hash('sha512', $secretKey))
                    ->getToken($config->signer(), $config->signingKey());
                $refreshTokenObj = $config->builder()
                    ->expiresAt($now->modify('+'. config('jwt.connector.refresh_ttl') .' minutes'))
                    ->withClaim('secret', hash('sha512', $secretKey))
                    ->getToken($config->signer(), $config->signingKey());

                $input['endpoint'] = url('/api/v1/connectors');
                $input['key'] = $secretKey;
                $input['token'] = $tokenObj->toString();
                $input['refresh_token'] = $refreshTokenObj->toString();
            }

            $connector = $this->settingConnectorRepository->create($input);

            if($input['provider'] == SettingConnector::PROVIDER_CUSTOM) {
                $connector->endpoint = url('/api/v1/connectors/' . $connector->id);
                $connector->save();
            }

            \DB::commit();

            $data = [
                'workspace_id' => $this->tmpWorkspace->id,
            ];

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('setting.connectors.manager.created_successfully'));
            }

            Flash::success(trans('setting.connectors.manager.created_successfully'));
            return redirect(route($this->guard.'.settings.connector.index'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Show the form for editing the specified challenge.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit(int $id)
    {
        $settingConnector = $this->settingConnectorRepository->findWithoutFail($id);

        if (empty($settingConnector)) {
            Flash::error(trans('setting.connectors.manager.not_found'));
            return redirect(route($this->guard.'.settings.connector.index'));
        }

        if($settingConnector->provider == SettingConnector::PROVIDER_CUSTOM) {
            $secretKey = $settingConnector->key;
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secretKey));
            $now = new \DateTimeImmutable();
            $tokenObj = $config->builder()
                ->expiresAt($now->modify('+'. config('jwt.connector.ttl') .' minutes'))
                ->withClaim('secret', hash('sha512', $secretKey))
                ->getToken($config->signer(), $config->signingKey());
            $refreshTokenObj = $config->builder()
                ->expiresAt($now->modify('+'. config('jwt.connector.refresh_ttl') .' minutes'))
                ->withClaim('secret', hash('sha512', $secretKey))
                ->getToken($config->signer(), $config->signingKey());
            $accessToken = $tokenObj->toString();
            $refreshToken = $refreshTokenObj->toString();
            $settingConnector->token = $accessToken;
            $settingConnector->refresh_token = $refreshToken;
            $settingConnector->save();
        }

        return response()->json([
            'code' => 200,
            'data' => view('manager.settings.connectors.partials.form', [
                'action'     => route($this->guard . '.settings.connector.update', ['id' => $settingConnector->id]),
                'method'     => 'PUT',
                'idForm'     => 'update_option',
                'titleModal' => trans('setting.connectors.manager.edit_connector'),
                'settingConnector' => $settingConnector,
                'workspaceId' => $this->tmpWorkspace->id
            ])->render(),
        ]);
    }

    /**
     * Update the specified challenge in storage.
     *
     * @param int $workspaceId
     * @param  int $id
     * @param UpdateSettingConnectorRequest $request
     *
     * @return Response
     */
    public function update(int $id, UpdateSettingConnectorRequest $request)
    {
        $settingConnector = $this->settingConnectorRepository->findWithoutFail($id);

        if (empty($settingConnector)) {
            Flash::error(trans('setting.connectors.manager.not_found'));
            return redirect(route($this->guard.'.settings.connector.index'));
        }

        try {
            \DB::beginTransaction();

            $input = $request->all();
            $this->settingConnectorRepository->update($input, $id);

            \DB::commit();

            $data = [
                'workspace_id' => $this->tmpWorkspace->id,
            ];

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('setting.connectors.manager.updated_successfully'));
            }

            Flash::success(trans('setting.connectors.manager.updated_successfully'));
            return redirect(route($this->guard.'.settings.connector.index'));
        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Remove the specified challenge from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy(int $id, Request $request)
    {
        $settingConnector = $this->settingConnectorRepository->findWithoutFail($id);

        if (empty($settingConnector)) {
            Flash::error(trans('setting.connectors.manager.not_found'));
            return redirect(route($this->guard.'.settings.connector.index'));
        }

        try {
            \DB::beginTransaction();
            $this->settingConnectorRepository->delete($id);
            \DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => trans('option.deleted_confirm'),
                ]);
            }

            Flash::success(trans('setting.connectors.manager.deleted_successfully'));
            return redirect(route($this->guard.'.settings.connector.index'));
        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getTraceAsString());

            return response()->json([
                'status'  => "failed",
                'message' => $exc->getMessage(),
            ]);
        }
    }
}
