<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationCategoryAPIRequest;
use App\Http\Requests\API\UpdateNotificationCategoryAPIRequest;
use App\Models\NotificationCategory;
use App\Repositories\NotificationCategoryRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class NotificationCategoryController
 * @package App\Http\Controllers\API
 */
class NotificationCategoryAPIController extends AppBaseController
{
    /** @var  NotificationCategoryRepository */
    private $notificationCategoryRepository;

    public function __construct(NotificationCategoryRepository $notificationCategoryRepo)
    {
        parent::__construct();

        $this->notificationCategoryRepository = $notificationCategoryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->notificationCategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->notificationCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $notificationCategories = $this->notificationCategoryRepository->paginate($limit);

        return $this->sendResponse($notificationCategories->toArray(), trans('notification_category.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateNotificationCategoryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateNotificationCategoryAPIRequest $request)
    {
        $input = $request->all();

        $notificationCategory = $this->notificationCategoryRepository->create($input);

        return $this->sendResponse($notificationCategory->toArray(), trans('notification_category.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var NotificationCategory $notificationCategory */
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            return $this->sendError(trans('notification_category.not_found'));
        }

        return $this->sendResponse($notificationCategory->toArray(), trans('notification_category.message_retrieved_successfully'));
    }

    /**
     * @param UpdateNotificationCategoryAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateNotificationCategoryAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var NotificationCategory $notificationCategory */
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            return $this->sendError(trans('notification_category.not_found'));
        }

        $notificationCategory = $this->notificationCategoryRepository->update($input, $id);

        return $this->sendResponse($notificationCategory->toArray(), trans('notification_category.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var NotificationCategory $notificationCategory */
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            return $this->sendError(trans('notification_category.not_found'));
        }

        $notificationCategory->delete();

        return $this->sendResponse($id, trans('notification_category.message_deleted_successfully'));
    }
}
