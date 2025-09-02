<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateSettingPaymentRequest;
use App\Http\Requests\UpdateSettingPaymentRequest;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\SettingPaymentRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SettingPaymentController extends BaseController
{
    /** @var  SettingPaymentRepository */
    private $settingPaymentRepository;

    /** @var SettingConnectorRepository */
    private $settingConnectorRepository;

    public function __construct(
        SettingPaymentRepository $settingPaymentRepo,
        SettingConnectorRepository $settingConnectorRepo
    )
    {
        parent::__construct();

        $this->settingPaymentRepository = $settingPaymentRepo;
        $this->settingConnectorRepository = $settingConnectorRepo;
    }

    /**
     * Display a listing of the SettingPayment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->settingPaymentRepository->pushCriteria(new RequestCriteria($request));
        $settingPayments = $this->settingPaymentRepository->all();

        return view('admin.setting_payments.index')
            ->with('settingPayments', $settingPayments);
    }

    /**
     * Show the form for creating a new SettingPayment.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.setting_payments.create');
    }

    /**
     * Store a newly created SettingPayment in storage.
     *
     * @param CreateSettingPaymentRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingPaymentRequest $request)
    {
        $input = $request->all();

        $settingPayment = $this->settingPaymentRepository->create($input);

        Flash::success(trans('setting_payment.message_saved_successfully'));

        return redirect(route('admin.settingPayments.index'));
    }

    /**
     * Display the specified SettingPayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            Flash::error(trans('setting_payment.not_found'));

            return redirect(route('admin.settingPayments.index'));
        }

        return view('admin.setting_payments.show')->with('settingPayment', $settingPayment);
    }

    /**
     * Show the form for editing the specified SettingPayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            Flash::error(trans('setting_payment.not_found'));

            return redirect(route('admin.settingPayments.index'));
        }

        return view('admin.setting_payments.edit')->with('settingPayment', $settingPayment);
    }

    /**
     * Update the specified SettingPayment in storage.
     *
     * @param  int              $id
     * @param UpdateSettingPaymentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingPaymentRequest $request)
    {
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            Flash::error(trans('setting_payment.not_found'));

            return redirect(route('admin.settingPayments.index'));
        }

        $settingPayment = $this->settingPaymentRepository->update($request->all(), $id);

        Flash::success(trans('setting_payment.message_updated_successfully'));

        return redirect(route('admin.settingPayments.index'));
    }

    /**
     * Remove the specified SettingPayment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            Flash::error(trans('setting_payment.not_found'));

            return redirect(route('admin.settingPayments.index'));
        }

        $this->settingPaymentRepository->delete($id);

        Flash::success(trans('setting_payment.message_deleted_successfully'));

        return redirect(route('admin.settingPayments.index'));
    }

    /**
     * @param int $workspaceId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreate(int $workspaceId, Request $request)
    {
        $input = $request->all();
        $input['workspace_id'] = $this->tmpWorkspace->id;
        $payment = $this->settingPaymentRepository->updateOrCreatePayments($input);

        $connectorsList = $this->getConnectorsList();
        $this->settingPaymentRepository->updateOrCreatePaymentReferences($input, $this->tmpWorkspace->id, $connectorsList);

        return $this->sendResponse([], trans('setting.more.updated_confirm'));
    }

    /**
     * @return mixed|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }
}
