<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateSettingDeliveryConditionsRequest;
use App\Http\Requests\UpdateSettingDeliveryConditionsRequest;
use App\Repositories\SettingDeliveryConditionsRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SettingDeliveryConditionsController extends BaseController
{
    /** @var  SettingDeliveryConditionsRepository */
    private $settingDeliveryConditionsRepository;

    public function __construct(SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo)
    {
        parent::__construct();

        $this->settingDeliveryConditionsRepository = $settingDeliveryConditionsRepo;
    }

    /**
     * Display a listing of the SettingDeliveryConditions.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->settingDeliveryConditionsRepository->pushCriteria(new RequestCriteria($request));
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->all();

        return view('admin.setting_delivery_conditions.index')
            ->with('settingDeliveryConditions', $settingDeliveryConditions);
    }

    /**
     * Show the form for creating a new SettingDeliveryConditions.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.setting_delivery_conditions.create');
    }

    /**
     * Store a newly created SettingDeliveryConditions in storage.
     *
     * @param CreateSettingDeliveryConditionsRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingDeliveryConditionsRequest $request)
    {
        $input = $request->all();

        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->create($input);

        Flash::success(trans('setting_delivery_condition.message_saved_successfully'));

        return redirect(route('admin.settingDeliveryConditions.index'));
    }

    /**
     * Display the specified SettingDeliveryConditions.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            Flash::error(trans('setting_delivery_condition.not_found'));

            return redirect(route('admin.settingDeliveryConditions.index'));
        }

        return view('admin.setting_delivery_conditions.show')->with('settingDeliveryConditions', $settingDeliveryConditions);
    }

    /**
     * Show the form for editing the specified SettingDeliveryConditions.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            Flash::error(trans('setting_delivery_condition.not_found'));

            return redirect(route('admin.settingDeliveryConditions.index'));
        }

        return view('admin.setting_delivery_conditions.edit')->with('settingDeliveryConditions', $settingDeliveryConditions);
    }

    /**
     * Update the specified SettingDeliveryConditions in storage.
     *
     * @param  int              $id
     * @param UpdateSettingDeliveryConditionsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingDeliveryConditionsRequest $request)
    {
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            Flash::error(trans('setting_delivery_condition.not_found'));

            return redirect(route('admin.settingDeliveryConditions.index'));
        }

        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->update($request->all(), $id);

        Flash::success(trans('setting_delivery_condition.message_updated_successfully'));

        return redirect(route('admin.settingDeliveryConditions.index'));
    }

    /**
     * Remove the specified SettingDeliveryConditions from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            Flash::error(trans('setting_delivery_condition.not_found'));

            return redirect(route('admin.settingDeliveryConditions.index'));
        }

        $this->settingDeliveryConditionsRepository->delete($id);

        Flash::success(trans('setting_delivery_condition.message_deleted_successfully'));

        return redirect(route('admin.settingDeliveryConditions.index'));
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
        
        //Check overlap and return
        if (Helper::checkOverlap($input['delivery'])) {
            return $this->sendError(trans('setting.more.is_overlap'), 400, []);
        }
        
        $delivery = $this->settingDeliveryConditionsRepository->updateOrCreateDelivery($input);

        return $this->sendResponse([], trans('setting.more.delivery_updated_confirm'));
    }
}