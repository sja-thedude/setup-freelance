<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingTimeslotAPIRequest;
use App\Http\Requests\API\UpdateSettingTimeslotAPIRequest;
use App\Models\SettingTimeslot;
use App\Repositories\SettingTimeslotRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingTimeslotController
 * @package App\Http\Controllers\API
 */
class SettingTimeslotAPIController extends AppBaseController
{
    /** @var  SettingTimeslotRepository */
    private $settingTimeslotRepository;

    public function __construct(SettingTimeslotRepository $settingTimeslotRepo)
    {
        parent::__construct();

        $this->settingTimeslotRepository = $settingTimeslotRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingTimeslotRepository->pushCriteria(new RequestCriteria($request));
            $this->settingTimeslotRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingTimeslots = $this->settingTimeslotRepository->paginate($limit);

        return $this->sendResponse($settingTimeslots->toArray(), trans('setting_timeslot.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingTimeslotAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingTimeslotAPIRequest $request)
    {
        $input = $request->all();

        $settingTimeslot = $this->settingTimeslotRepository->create($input);

        return $this->sendResponse($settingTimeslot->toArray(), trans('setting_timeslot.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingTimeslot $settingTimeslot */
        $settingTimeslot = $this->settingTimeslotRepository->findWithoutFail($id);

        if (empty($settingTimeslot)) {
            return $this->sendError(trans('setting_timeslot.not_found'));
        }

        return $this->sendResponse($settingTimeslot->toArray(), trans('setting_timeslot.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingTimeslotAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingTimeslotAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingTimeslot $settingTimeslot */
        $settingTimeslot = $this->settingTimeslotRepository->findWithoutFail($id);

        if (empty($settingTimeslot)) {
            return $this->sendError(trans('setting_timeslot.not_found'));
        }

        $settingTimeslot = $this->settingTimeslotRepository->update($input, $id);

        return $this->sendResponse($settingTimeslot->toArray(), trans('setting_timeslot.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingTimeslot $settingTimeslot */
        $settingTimeslot = $this->settingTimeslotRepository->findWithoutFail($id);

        if (empty($settingTimeslot)) {
            return $this->sendError(trans('setting_timeslot.not_found'));
        }

        $settingTimeslot->delete();

        return $this->sendResponse($id, trans('setting_timeslot.message_deleted_successfully'));
    }
}
