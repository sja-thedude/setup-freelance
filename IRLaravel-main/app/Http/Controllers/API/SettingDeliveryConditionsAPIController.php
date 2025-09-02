<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingDeliveryConditionsAPIRequest;
use App\Http\Requests\API\UpdateSettingDeliveryConditionsAPIRequest;
use App\Models\SettingDeliveryConditions;
use App\Repositories\SettingDeliveryConditionsRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingDeliveryConditionsController
 * @package App\Http\Controllers\API
 */
class SettingDeliveryConditionsAPIController extends AppBaseController
{
    /** @var  SettingDeliveryConditionsRepository */
    private $settingDeliveryConditionsRepository;

    public function __construct(SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo)
    {
        parent::__construct();

        $this->settingDeliveryConditionsRepository = $settingDeliveryConditionsRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingDeliveryConditionsRepository->pushCriteria(new RequestCriteria($request));
            $this->settingDeliveryConditionsRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->paginate($limit);

        return $this->sendResponse($settingDeliveryConditions->toArray(), trans('setting_delivery_condition.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingDeliveryConditionsAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingDeliveryConditionsAPIRequest $request)
    {
        $input = $request->all();

        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->create($input);

        return $this->sendResponse($settingDeliveryConditions->toArray(), trans('setting_delivery_condition.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingDeliveryConditions $settingDeliveryConditions */
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            return $this->sendError(trans('setting_delivery_condition.not_found'));
        }

        return $this->sendResponse($settingDeliveryConditions->toArray(), trans('setting_delivery_condition.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingDeliveryConditionsAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingDeliveryConditionsAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingDeliveryConditions $settingDeliveryConditions */
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            return $this->sendError(trans('setting_delivery_condition.not_found'));
        }

        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->update($input, $id);

        return $this->sendResponse($settingDeliveryConditions->toArray(), trans('setting_delivery_condition.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingDeliveryConditions $settingDeliveryConditions */
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWithoutFail($id);

        if (empty($settingDeliveryConditions)) {
            return $this->sendError(trans('setting_delivery_condition.not_found'));
        }

        $settingDeliveryConditions->delete();

        return $this->sendResponse($id, trans('setting_delivery_condition.message_deleted_successfully'));
    }
}
