<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateSettingGeneralRequest;
use App\Http\Requests\UpdateSettingGeneralRequest;
use App\Repositories\SettingGeneralRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SettingGeneralController extends BaseController
{
    /** @var  SettingGeneralRepository */
    private $settingGeneralRepository;

    public function __construct(SettingGeneralRepository $settingGeneralRepo)
    {
        parent::__construct();

        $this->settingGeneralRepository = $settingGeneralRepo;
    }

    /**
     * Display a listing of the SettingGeneral.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->settingGeneralRepository->pushCriteria(new RequestCriteria($request));
        $settingGenerals = $this->settingGeneralRepository->all();

        return view('admin.setting_generals.index')
            ->with('settingGenerals', $settingGenerals);
    }

    /**
     * Show the form for creating a new SettingGeneral.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.setting_generals.create');
    }

    /**
     * Store a newly created SettingGeneral in storage.
     *
     * @param CreateSettingGeneralRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingGeneralRequest $request)
    {
        $input = $request->all();

        $settingGeneral = $this->settingGeneralRepository->create($input);

        Flash::success(trans('setting_general.message_saved_successfully'));

        return redirect(route('admin.settingGenerals.index'));
    }

    /**
     * Display the specified SettingGeneral.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            Flash::error(trans('setting_general.not_found'));

            return redirect(route('admin.settingGenerals.index'));
        }

        return view('admin.setting_generals.show')->with('settingGeneral', $settingGeneral);
    }

    /**
     * Show the form for editing the specified SettingGeneral.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            Flash::error(trans('setting_general.not_found'));

            return redirect(route('admin.settingGenerals.index'));
        }

        return view('admin.setting_generals.edit')->with('settingGeneral', $settingGeneral);
    }

    /**
     * Update the specified SettingGeneral in storage.
     *
     * @param  int              $id
     * @param UpdateSettingGeneralRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingGeneralRequest $request)
    {
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            Flash::error(trans('setting_general.not_found'));

            return redirect(route('admin.settingGenerals.index'));
        }

        $settingGeneral = $this->settingGeneralRepository->update($request->all(), $id);

        Flash::success(trans('setting_general.message_updated_successfully'));

        return redirect(route('admin.settingGenerals.index'));
    }

    /**
     * Remove the specified SettingGeneral from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            Flash::error(trans('setting_general.not_found'));

            return redirect(route('admin.settingGenerals.index'));
        }

        $this->settingGeneralRepository->delete($id);

        Flash::success(trans('setting_general.message_deleted_successfully'));

        return redirect(route('admin.settingGenerals.index'));
    }

    /**
     * @param int $workspaceId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreate(int $workspaceId, Request $request)
    {
        $input = $request->all();

        $settingGeneral = $this->settingGeneralRepository->updateOrCreate(
            [
                'workspace_id' => $workspaceId,
            ],
            $input
        );

        return $this->sendResponse($settingGeneral->toArray(), trans('workspace.updated_confirm'));
    }
}
