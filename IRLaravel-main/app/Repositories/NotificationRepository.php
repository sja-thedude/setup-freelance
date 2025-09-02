<?php

namespace App\Repositories;

use App\Models\GroupRestaurant;
use App\Models\Notification;
use App\Models\NotificationDevice;
use Illuminate\Http\Request;

class NotificationRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notification_plan_id',
        'template_id',
        'platform',
        'status',
        'title',
        'description',
        'sent_time',
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Notification::class;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param \Illuminate\Http\Request|string|array $request
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    protected function commonFilter($model, $request)
    {
        $arrRequest = $request->all();

        // Filter by user_id
        if ($request->has('user_id')) {
            $model = $model->where('user_id', (int)$request->get('user_id'));
        }

        // Check by Base app or Template app
        if (array_key_exists('App-Token', $arrRequest) || array_key_exists('Group-Token', $arrRequest)) {
            // Get App-Token from request
            $appToken = array_get($arrRequest, 'App-Token');
            $groupToken = array_get($arrRequest, 'Group-Token');

            if (!empty($appToken)) {
                // Get workspace by App-Token
                /** @var \App\Models\Workspace $workspace */
                $workspace = \App\Models\Workspace::where('token', $appToken)->first();

                // Invalid workspace
                if (empty($workspace)) {
                    throw new \Exception(trans('workspace.not_found'), 500);
                }

                $model = $model->where('notifications.template_id', $workspace->id);
            } elseif (!empty($groupToken)) {
                $groupRestaurant = GroupRestaurant::whereToken($groupToken)->first();

                // Invalid group restaurant
                if (empty($groupRestaurant)) {
                    throw new \Exception(trans('grouprestaurant.not_found'), 500);
                }

                $model = $model->where('notifications.group_restaurant_id', $groupRestaurant->id);
            } else {
                $model = $model->whereNull('notifications.template_id')
                    ->whereNull('notifications.group_restaurant_id');
            }
        }

        // Filter by template_id
        if (array_key_exists('template_id', $arrRequest)) {
            $workspaceId = (int)array_get($arrRequest, 'template_id');

            if (!empty($workspaceId)) {
                $model = $model->where('notifications.template_id', $workspaceId);
            } else {
                $model = $model->whereNull('notifications.template_id');
            }
        }

        return $model;
    }

    /**
     * @overwrite
     *
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();

        // Filter
        $this->scopeQuery(function ($model) use ($request) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            // Prevent duplicate field
            $model = $model->select('notifications.*');

            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

            $model = $this->commonFilter($model, $request);

            // Order by from request
            if (!empty($orderBy)) {
                // Order by main table
                $model = $model->orderBy('notifications.' . $orderBy, $sortBy);
            } else {
                // Default order by
                $model = $model->orderBy('notifications.created_at', 'desc');
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * Count unread the notifications
     *
     * @param array $filter option param
     * @return int
     */
    public function countUnread($filter = array())
    {
        $request = request();

        if (!empty($filter)) {
            $request->merge($filter);
        }

        // Filter
        $this->scopeQuery(function ($model) use ($request) {
            /** @var \Illuminate\Database\Eloquent\Model $model */

            $model = $model->where('notifications.status', false);

            $model = $this->commonFilter($model, $request);

            return $model;
        });

        return $this->count();
    }

    /**
     * @param Request $request
     * @param int $userId
     * @param bool $checkCount
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function queryNotificationByUser(Request $request, $userId, $checkCount = false)
    {
        $model = $this->model
            ->where('user_id', $userId);

        if ($checkCount) {
            // Unread status
            $model = $model->where('status', Notification::INACTIVE);
        }

        $arrRequest = $request->all();

        // Filter by workspace
        if (array_key_exists('workspace_id', $arrRequest)) {
            $workspaceId = (int)$request->get('workspace_id');

            if (!empty($workspaceId)) {
                $model = $model->where('notifications.template_id', $workspaceId);
            } else {
                $model = $model->whereNull('notifications.template_id');
            }
        }

        return $model;
    }

    /**
     * @param Request $request
     * @param int $userId
     * @param bool $checkCount
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getNotificationByUser(Request $request, $userId, $checkCount = false)
    {
        $model = $this->queryNotificationByUser($request, $userId, $checkCount);

        $records = $model
            ->select('notifications.*')
            ->orderBy('notifications.created_at', 'desc')
            ->get();

        return $records;
    }

    /**
     * @param Request $request
     * @param int $userId
     * @param bool $checkCount
     * @return int
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function countNotificationByUser(Request $request, $userId, $checkCount = false)
    {
        $model = $this->queryNotificationByUser($request, $userId, $checkCount);

        $total = $model->count();

        return $total;
    }

    /**
     * Overwrite
     *
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function saveDevice(array $attributes)
    {
        // Default value
        if (!array_key_exists('notified', $attributes)) {
            $attributes['notified'] = true;
        }

        // Check device exist
        if (empty($attributes['user_id'])) {
            // Throw exception if missing parameter
            throw new \Exception('Missing parameter user_id', 500);
        }

        // Get workspace from token
        if (!empty($attributes['App-Token'])) {
            $appToken = $attributes['App-Token'];
            /** @var \App\Models\Workspace $workspace */
            $workspace = \App\Models\Workspace::whereToken($appToken)->first();

            if (empty($workspace)) {
                throw new \Exception(trans('workspace.not_found'), 500);
            }

            $attributes['template_id'] = $workspace->id;
            unset($attributes['App-Token']);
        }

        // Allow notification for group app
        if (!empty($attributes['Group-Token'])) {
            $groupToken = $attributes['Group-Token'];
            $groupRestaurant = GroupRestaurant::whereToken($groupToken)->first();

            if (empty($groupRestaurant)) {
                throw new \Exception(trans('grouprestaurant.not_found'), 500);
            }

            $attributes['group_restaurant_id'] = $groupRestaurant->id;
            unset($attributes['Group-Token']);
        }

        NotificationDevice::where('user_id', '!=', $attributes['user_id'])
            ->where(function ($query) use ($attributes) {
                $query->where('device_id', $attributes['device_id'])
                    ->orWhere('token', $attributes['token']);
            })->delete();

        if (empty($attributes['template_id'])) {
            unset($attributes['template_id']);
        }

        if (empty($attributes['group_restaurant_id'])) {
            unset($attributes['group_restaurant_id']);
        }

        $attributes['active'] = true;
        $model = NotificationDevice::updateOrCreate([
            'user_id' => $attributes['user_id'],
            'device_id' => $attributes['device_id']
        ], $attributes);

        // Throw exception if unable to save device
        if (empty($model)) {
            throw new \Exception('Unable to save device', 500);
        }

        return $model;
    }

    /**
     * Unsubscribe a device by user_id
     *
     * @param array $attributes
     * @return int
     */
    public function unsubscribeDevice(array $attributes)
    {
        $deleted = null;

        if (!empty($attributes['user_id'])) {
            // Delete device by user_id and device_id
            $deleted = NotificationDevice::where('user_id', $attributes['user_id'])
                ->where('device_id', $attributes['device_id'])
                ->delete();
        } else {
            // Delete all device by device_id
            $deleted = NotificationDevice::where('device_id', $attributes['device_id'])
                ->delete();
        }

        return $deleted;
    }

    /**
     * Change device status
     *
     * @param string $deviceId
     * @param bool $status
     * @return int Total device was changed status
     */
    public function changeDeviceStatus(string $deviceId, bool $status)
    {
        return NotificationDevice::where('device_id', $deviceId)
            ->update([
                'active' => $status,
            ]);
    }

    /**
     * Enable device to allow push notification
     *
     * @param string $deviceId
     * @return int Total device was changed status
     */
    public function enableDevice(string $deviceId)
    {
        return $this->changeDeviceStatus($deviceId, true);
    }

    /**
     * Disable device to prevent push notification
     *
     * @param string $deviceId
     * @return int Total device was changed status
     */
    public function disableDevice(string $deviceId)
    {
        return $this->changeDeviceStatus($deviceId, false);
    }

    /**
     * Mark as read all notification of user
     *
     * @param int $userId User ID
     * @param array|null $ids
     * @return int Total item was read
     */
    public function markAsRead(int $userId, array $ids = null)
    {
        $read = Notification::where('user_id', $userId);

        // Only with
        if (!empty($ids)) {
            $read->whereIn('id', $ids);
        }

        $read->update([
            'status' => true,
        ]);

        return $read;
    }

    /**
     * Apply notification
     *
     * @param array $attributes Filter conditions
     * @return void
     */
    public function applyNotification(array $attributes)
    {
        // Push notification
        dispatch(new \App\Jobs\PushNotification($attributes, \App::getLocale()));
    }

}
