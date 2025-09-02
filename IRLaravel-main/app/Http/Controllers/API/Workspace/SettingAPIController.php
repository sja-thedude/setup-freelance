<?php

namespace App\Http\Controllers\API\Workspace;

use App\Facades\Helper;
use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\GetDeliveryConditionAPIRequest;
use App\Http\Requests\API\TimeslotAPIRequest;
use App\Http\Requests\API\TimeslotOrderDaysAPIRequest;
use App\Repositories\SettingOpenHourRepository;
use App\Repositories\SettingPreferenceRepository;
use App\Repositories\SettingTimeslotDetailRepository;
use App\Repositories\SettingTimeslotRepository;
use App\Repositories\WorkspaceRepository;
use App\Repositories\ServiceCostRepository;
use Illuminate\Http\Request;

/**
 * Class WorkspaceController
 * @package App\Http\Controllers\API
 */
class SettingAPIController extends AppBaseController
{
    /** @var WorkspaceRepository $workspaceRepository */
    protected $workspaceRepository;

    /** @var SettingOpenHourRepository $settingOpenHourRepository */
    protected $settingOpenHourRepository;

    /** @var SettingPreferenceRepository $settingPreferenceRepository */
    protected $settingPreferenceRepository;

    /** @var SettingTimeslotDetailRepository $settingTimeslotDetailRepository */
    protected $settingTimeslotDetailRepository;

    /** @var SettingTimeslotRepository $settingTimeslotRepository */
    protected $settingTimeslotRepository;
    protected $serviceCostRepository;

    /**
     * WorkspaceAPIController constructor.
     * @param WorkspaceRepository $workspaceRepo
     * @param SettingOpenHourRepository $settingOpenHourRepo
     * @param SettingPreferenceRepository $settingPreferenceRepo
     * @param SettingTimeslotDetailRepository $settingTimeslotDetailRepo
     * @param SettingTimeslotRepository $settingTimeslotRepo
     */
    public function __construct(
        WorkspaceRepository $workspaceRepo,
        SettingOpenHourRepository $settingOpenHourRepo,
        SettingPreferenceRepository $settingPreferenceRepo,
        SettingTimeslotDetailRepository $settingTimeslotDetailRepo,
        SettingTimeslotRepository $settingTimeslotRepo,
        ServiceCostRepository $serviceCostRepo
    )
    {
        parent::__construct();

        $this->workspaceRepository = $workspaceRepo;
        $this->settingOpenHourRepository = $settingOpenHourRepo;
        $this->settingPreferenceRepository = $settingPreferenceRepo;
        $this->settingTimeslotDetailRepository = $settingTimeslotDetailRepo;
        $this->settingTimeslotRepository = $settingTimeslotRepo;
        $this->serviceCostRepository = $serviceCostRepo;
    }

    /**
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deliveryConditions(GetDeliveryConditionAPIRequest $request, int $workspaceId)
    {
        // Current location
        $location = [
            'lat' => $request->get('lat'),
            'lng' => $request->get('lng'),
        ];
        $conditions = $this->workspaceRepository->getDeliveryConditions($workspaceId, $location);

        $conditions->transform(function ($item) {
            /** @var \App\Models\SettingDeliveryConditions $item */
            return $item->getFullInfo();
        });
        $result = $conditions->toArray();

        return $this->sendResponse($result, trans('delivery_condition.message_retrieved_list_successfully'));
    }

    /**
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function minDeliveryCondition(int $workspaceId)
    {
        $result = $this->workspaceRepository->getMinDeliveryCondition($workspaceId);

        return $this->sendResponse($result, trans('delivery_condition.message_retrieved_successfully'));
    }

    /**
     * Get opening hour settings
     *
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function openingHours(int $workspaceId)
    {
        $result = $this->settingOpenHourRepository->getOpenHourSettings($workspaceId);

        return $this->sendResponse($result, trans('setting_open_hour.message_retrieved_list_successfully'));
    }

    /**
     * Get opening hour settings
     *
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function holidayExceptions(int $workspaceId)
    {
        $result = $this->settingOpenHourRepository->getHolidayExceptions($workspaceId);

        return $this->sendResponse($result, trans('setting.message_retrieved_list_successfully'));
    }

    /**
     * Get opening hour settings
     *
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function preferences(Request $request, int $workspaceId)
    {
        $request->merge([
            'workspace_id' => $workspaceId,
        ]);
        $settings = $this->settingPreferenceRepository
            ->where('workspace_id', $workspaceId)
            ->firstOrNew([]);

        $result = $settings->getFullInfo();

        return $this->sendResponse($result, trans('setting.message_retrieved_successfully'));
    }

    /**
     * @param TimeslotAPIRequest $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function timeslots(TimeslotAPIRequest $request, int $workspaceId)
    {
        $date = $request->get('date');
        $type = $request->get('type');

        $timeslotSettings = $this->settingTimeslotRepository
            ->where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->firstOrFail();
        // Settings
        $result = $timeslotSettings->getFullInfo();

        // Timeslot items
        $timeslots = $this->settingTimeslotDetailRepository->getTimeslotsByDate($workspaceId, $date, $type);
        // Calculate timeslots by orders]
        $timeslotIds = $timeslots->pluck('id')->toArray();
        $timeslotsOrder = $this->settingTimeslotDetailRepository->calculateTimeslotsByOrders($timeslotIds);

        $timeslots->transform(function ($item) use ($timeslotsOrder) {
            /** @var \App\Models\SettingTimeslotDetail $item */

            return array_merge($item->getFullInfo(), [
                'current_order' => array_get($timeslotsOrder, $item->id . '.current_order', 0),
                'current_price' => array_get($timeslotsOrder, $item->id . '.current_price', 0),
            ]);
        });

        // Push items to setting
        $result = array_merge($result, [
            'timeslots' => $timeslots->toArray(),
        ]);

        return $this->sendResponse($result, trans('setting.message_retrieved_list_successfully'));
    }

    /**
     * @param TimeslotOrderDaysAPIRequest $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTimeslotOrderDays(TimeslotOrderDaysAPIRequest $request, int $workspaceId)
    {
        $type = $request->get('type');
        $options = [
            'timezone' => Helper::getAppTimezone($request),
        ];

        try {
            $result = $this->settingTimeslotDetailRepository->checkTimeslotOrderDays($workspaceId, $type, $options);
        } catch (\Exception $e) {
            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;
            return $this->sendError($e->getMessage(), $errorCode);
        }

        return $this->sendResponse($result, trans('setting.message_retrieved_list_successfully'));
    }

    /**
     * @param $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function serviceCostSetting($workspaceId) {
        $setting = $this->serviceCostRepository->serviceCostEnabled($workspaceId);

        return $this->sendResponse($setting, trans('setting.message_get_service_cost_setting_successfully'));
    }
}
