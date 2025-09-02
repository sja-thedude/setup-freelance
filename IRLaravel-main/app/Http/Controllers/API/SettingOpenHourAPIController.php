<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingOpenHourAPIRequest;
use App\Http\Requests\API\UpdateSettingOpenHourAPIRequest;
use App\Models\SettingOpenHour;
use App\Repositories\SettingOpenHourRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingOpenHourController
 * @package App\Http\Controllers\API
 */
class SettingOpenHourAPIController extends AppBaseController
{
    /** @var  SettingOpenHourRepository */
    private $settingOpenHourRepository;

    public function __construct(SettingOpenHourRepository $settingOpenHourRepo)
    {
        parent::__construct();

        $this->settingOpenHourRepository = $settingOpenHourRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingOpenHourRepository->pushCriteria(new RequestCriteria($request));
            $this->settingOpenHourRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingOpenHours = $this->settingOpenHourRepository->paginate($limit);

        return $this->sendResponse($settingOpenHours->toArray(), trans('setting_open_hour.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingOpenHourAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingOpenHourAPIRequest $request)
    {
        $input = $request->all();

        $settingOpenHour = $this->settingOpenHourRepository->create($input);

        return $this->sendResponse($settingOpenHour->toArray(), trans('setting_open_hour.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingOpenHour $settingOpenHour */
        $settingOpenHour = $this->settingOpenHourRepository->findWithoutFail($id);

        if (empty($settingOpenHour)) {
            return $this->sendError(trans('setting_open_hour.not_found'));
        }

        return $this->sendResponse($settingOpenHour->toArray(), trans('setting_open_hour.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingOpenHourAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingOpenHourAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingOpenHour $settingOpenHour */
        $settingOpenHour = $this->settingOpenHourRepository->findWithoutFail($id);

        if (empty($settingOpenHour)) {
            return $this->sendError(trans('setting_open_hour.not_found'));
        }

        $settingOpenHour = $this->settingOpenHourRepository->update($input, $id);

        return $this->sendResponse($settingOpenHour->toArray(), trans('setting_open_hour.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingOpenHour $settingOpenHour */
        $settingOpenHour = $this->settingOpenHourRepository->findWithoutFail($id);

        if (empty($settingOpenHour)) {
            return $this->sendError(trans('setting_open_hour.not_found'));
        }

        $settingOpenHour->delete();

        return $this->sendResponse($id, trans('setting_open_hour.message_deleted_successfully'));
    }
}
