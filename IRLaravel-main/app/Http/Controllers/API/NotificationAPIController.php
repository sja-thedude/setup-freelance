<?php

namespace App\Http\Controllers\API;

use App\Facades\Helper;
use App\Http\Requests\API\CreateNotificationAPIRequest;
use App\Http\Requests\API\DisableNotificationDeviceAPIRequest;
use App\Http\Requests\API\EnableNotificationDeviceAPIRequest;
use App\Http\Requests\API\SaveNotificationDeviceAPIRequest;
use App\Http\Requests\API\UnsubscribeNotificationDeviceAPIRequest;
use App\Http\Requests\API\UpdateNotificationAPIRequest;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */
class NotificationAPIController extends AppBaseController
{
    /**
     * @var NotificationRepository $notificationRepository
     */
    protected $notificationRepository;

    /**
     * NotificationAPIController constructor.
     * @param NotificationRepository $notificationRepo
     */
    public function __construct(NotificationRepository $notificationRepo)
    {
        parent::__construct();

        $this->notificationRepository = $notificationRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
        $requestOptions = [
            'user_id' => Auth::id(),
        ];

        // Request App token
        $appToken = Helper::getAppToken($request);
        $groupToken = Helper::getGroupToken($request);

        if (!empty($appToken)) {
            $requestOptions['App-Token'] = $appToken;
        }

        if (!empty($groupToken)) {
            $requestOptions['Group-Token'] = $groupToken;
        }

        $request->merge($requestOptions);

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $this->notificationRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notifications = $this->notificationRepository->paginate($limit);

        $notifications->transform(function (Notification $item) {
            return $item->getFullInfo();
        });
        $result = $notifications->toArray();

        // Addition more information to notification list
        $result = array_merge($result, [
            'total_unread' => $this->notificationRepository->countUnread(),
        ]);

        return $this->sendResponse($result, trans('notification.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateNotificationAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateNotificationAPIRequest $request)
    {
        $input = $request->all();

        $notification = $this->notificationRepository->create($input);

        return $this->sendResponse($notification->toArray(), trans('notification.message_created_successfully'));
    }

    /**
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Notification $notification)
    {
        $result = $notification->getFullInfo();

        return $this->sendResponse($result, trans('notification.message_retrieved_successfully'));
    }

    /**
     * @param UpdateNotificationAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateNotificationAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Notification $notification */
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            return $this->sendError(trans('notification.not_found'));
        }

        $notification = $this->notificationRepository->update($input, $id);

        return $this->sendResponse($notification->toArray(), trans('notification.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            return $this->sendError(trans('notification.not_found'));
        }

        $notification->delete();

        return $this->sendResponse($id, trans('notification.message_deleted_successfully'));
    }

    /**
     * Create or Update a Notification Device
     *
     * @param SaveNotificationDeviceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function device(SaveNotificationDeviceAPIRequest $request)
    {
        $input = $request->all();

        // Get App-Token from request
        $appToken = Helper::getAppToken($request);

        if (!empty($appToken)) {
            $input['App-Token'] = $appToken;
        }

        // Get Group-Token from request
        $groupToken = Helper::getGroupToken($request);

        if (!empty($groupToken)) {
            $input['Group-Token'] = $groupToken;
        }

        try {
            // Get user logged in
            $user = $this->notificationRepository->getJWTAuth(true);
            $input = array_merge($input, [
                'user_id' => $user->id,
            ]);

            $device = $this->notificationRepository->saveDevice($input);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

        $result = $device->toArray();

        return $this->sendResponse($result, trans('notification.saved_device_successfully'));
    }

    /**
     * Create or Update a Notification Device
     *
     * @param SaveNotificationDeviceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribeDevice(UnsubscribeNotificationDeviceAPIRequest $request)
    {
        $input = $request->all();

        try {
            // Get user logged in
            $user = $this->notificationRepository->getJWTAuth(true);
            $input = array_merge($input, [
                'user_id' => $user->id,
            ]);

            $device = $this->notificationRepository->unsubscribeDevice($input);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

        return $this->sendResponse(null, trans('notification.unsubscribe_device_successfully'));
    }

    /**
     * Enable a device by Device ID
     *
     * @param EnableNotificationDeviceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableDevice(EnableNotificationDeviceAPIRequest $request)
    {
        $deviceId = $request->get('device_id');

        $result = $this->notificationRepository->enableDevice($deviceId);

        return $this->sendResponse(null, trans('notification.message_enable_device_successfully'));
    }

    /**
     * Disable a device by Device ID
     *
     * @param DisableNotificationDeviceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disableDevice(DisableNotificationDeviceAPIRequest $request)
    {
        $deviceId = $request->get('device_id');

        $result = $this->notificationRepository->disableDevice($deviceId);

        return $this->sendResponse(null, trans('notification.message_disable_device_successfully'));
    }

    /**
     * Mark as read all notification of user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Auth::user();

        $notificationIds = $request->get('id');

        if (is_string($notificationIds)) {
            // Convert to array
            $notificationIds = [$notificationIds];
        }

        $result = $this->notificationRepository->markAsRead($user->id, $notificationIds);

        return $this->sendResponse(null, trans('notification.message_read_all_successfully'));
    }

}
