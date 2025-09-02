<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationPlanAPIRequest;
use App\Http\Requests\API\UpdateNotificationPlanAPIRequest;
use App\Models\NotificationPlan;
use App\Repositories\NotificationPlanRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class NotificationPlanController
 * @package App\Http\Controllers\API
 */
class NotificationPlanAPIController extends AppBaseController
{
    /** @var  NotificationPlanRepository */
    private $notificationPlanRepository;

    public function __construct(NotificationPlanRepository $notificationPlanRepo)
    {
        parent::__construct();

        $this->notificationPlanRepository = $notificationPlanRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->notificationPlanRepository->pushCriteria(new RequestCriteria($request));
            $this->notificationPlanRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $notificationPlans = $this->notificationPlanRepository->paginate($limit);

        return $this->sendResponse($notificationPlans->toArray(), trans('notification_plan.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateNotificationPlanAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateNotificationPlanAPIRequest $request)
    {
        $input = $request->all();

        $notificationPlan = $this->notificationPlanRepository->create($input);

        return $this->sendResponse($notificationPlan->toArray(), trans('notification_plan.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var NotificationPlan $notificationPlan */
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            return $this->sendError(trans('notification_plan.not_found'));
        }

        return $this->sendResponse($notificationPlan->toArray(), trans('notification_plan.message_retrieved_successfully'));
    }

    /**
     * @param UpdateNotificationPlanAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateNotificationPlanAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var NotificationPlan $notificationPlan */
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            return $this->sendError(trans('notification_plan.not_found'));
        }

        $notificationPlan = $this->notificationPlanRepository->update($input, $id);

        return $this->sendResponse($notificationPlan->toArray(), trans('notification_plan.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var NotificationPlan $notificationPlan */
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            return $this->sendError(trans('notification_plan.not_found'));
        }

        $notificationPlan->delete();

        return $this->sendResponse($id, trans('notification_plan.message_created_successfully'));
    }
}
