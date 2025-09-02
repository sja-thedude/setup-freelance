<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingExceptHourAPIRequest;
use App\Http\Requests\API\UpdateSettingExceptHourAPIRequest;
use App\Models\SettingExceptHour;
use App\Repositories\SettingExceptHourRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingExceptHourController
 * @package App\Http\Controllers\API
 */
class SettingExceptHourAPIController extends AppBaseController
{
    /** @var  SettingExceptHourRepository */
    private $settingExceptHourRepository;

    public function __construct(SettingExceptHourRepository $settingExceptHourRepo)
    {
        parent::__construct();

        $this->settingExceptHourRepository = $settingExceptHourRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingExceptHourRepository->pushCriteria(new RequestCriteria($request));
            $this->settingExceptHourRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingExceptHours = $this->settingExceptHourRepository->paginate($limit);

        return $this->sendResponse($settingExceptHours->toArray(), trans('setting_except_hour.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingExceptHourAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingExceptHourAPIRequest $request)
    {
        $input = $request->all();

        $settingExceptHour = $this->settingExceptHourRepository->create($input);

        return $this->sendResponse($settingExceptHour->toArray(), trans('setting_except_hour.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingExceptHour $settingExceptHour */
        $settingExceptHour = $this->settingExceptHourRepository->findWithoutFail($id);

        if (empty($settingExceptHour)) {
            return $this->sendError(trans('setting_except_hour.not_found'));
        }

        return $this->sendResponse($settingExceptHour->toArray(), trans('setting_except_hour.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingExceptHourAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingExceptHourAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingExceptHour $settingExceptHour */
        $settingExceptHour = $this->settingExceptHourRepository->findWithoutFail($id);

        if (empty($settingExceptHour)) {
            return $this->sendError(trans('setting_except_hour.not_found'));
        }

        $settingExceptHour = $this->settingExceptHourRepository->update($input, $id);

        return $this->sendResponse($settingExceptHour->toArray(), trans('setting_except_hour.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingExceptHour $settingExceptHour */
        $settingExceptHour = $this->settingExceptHourRepository->findWithoutFail($id);

        if (empty($settingExceptHour)) {
            return $this->sendError(trans('setting_except_hour.not_found'));
        }

        $settingExceptHour->delete();

        return $this->sendResponse($id, trans('setting_except_hour.message_deleted_successfully'));
    }
}
