<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingTimeslotDetailAPIRequest;
use App\Http\Requests\API\UpdateSettingTimeslotDetailAPIRequest;
use App\Models\SettingTimeslotDetail;
use App\Repositories\SettingTimeslotDetailRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingTimeslotDetailController
 * @package App\Http\Controllers\API
 */
class SettingTimeslotDetailAPIController extends AppBaseController
{
    /** @var  SettingTimeslotDetailRepository */
    private $settingTimeslotDetailRepository;

    public function __construct(SettingTimeslotDetailRepository $settingTimeslotDetailRepo)
    {
        parent::__construct();

        $this->settingTimeslotDetailRepository = $settingTimeslotDetailRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingTimeslotDetailRepository->pushCriteria(new RequestCriteria($request));
            $this->settingTimeslotDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingTimeslotDetails = $this->settingTimeslotDetailRepository->paginate($limit);

        return $this->sendResponse($settingTimeslotDetails->toArray(), trans('setting_timeslot_detail.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingTimeslotDetailAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingTimeslotDetailAPIRequest $request)
    {
        $input = $request->all();

        $settingTimeslotDetail = $this->settingTimeslotDetailRepository->create($input);

        return $this->sendResponse($settingTimeslotDetail->toArray(), trans('setting_timeslot_detail.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingTimeslotDetail $settingTimeslotDetail */
        $settingTimeslotDetail = $this->settingTimeslotDetailRepository->findWithoutFail($id);

        if (empty($settingTimeslotDetail)) {
            return $this->sendError(trans('setting_timeslot_detail.not_found'));
        }

        return $this->sendResponse($settingTimeslotDetail->toArray(), trans('setting_timeslot_detail.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingTimeslotDetailAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingTimeslotDetailAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingTimeslotDetail $settingTimeslotDetail */
        $settingTimeslotDetail = $this->settingTimeslotDetailRepository->findWithoutFail($id);

        if (empty($settingTimeslotDetail)) {
            return $this->sendError(trans('setting_timeslot_detail.not_found'));
        }

        $settingTimeslotDetail = $this->settingTimeslotDetailRepository->update($input, $id);

        return $this->sendResponse($settingTimeslotDetail->toArray(), trans('setting_timeslot_detail.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingTimeslotDetail $settingTimeslotDetail */
        $settingTimeslotDetail = $this->settingTimeslotDetailRepository->findWithoutFail($id);

        if (empty($settingTimeslotDetail)) {
            return $this->sendError(trans('setting_timeslot_detail.not_found'));
        }

        $settingTimeslotDetail->delete();

        return $this->sendResponse($id, trans('setting_timeslot_detail.message_deleted_successfully'));
    }
}
