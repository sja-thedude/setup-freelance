<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\NotificationCategory;
use App\Models\NotificationDevice;
use App\Models\NotificationPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\NotificationRepository;
use App\Services\Socket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * Class PushNotification
 * @package App\Jobs
 */
class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 115;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var NotificationRepository $notificationRepository
     */
    protected $notificationRepository;

    /**
     * @var NotificationPlan $notificationPlan
     */
    protected $notificationPlan;

    const LIMIT_USER = 20;

    /**
     * Create a new job instance.
     *
     * @param array $options
     * @param string|null $locale
     */
    public function __construct(array $options, $locale = null)
    {
        $this->options = $options;

        // Multi-language (locale)
        if (!empty($locale)) {
            \App::setLocale($locale);
        }
    }

    /**
     * Execute the job.
     *
     * @param NotificationRepository $notificationRepo
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(NotificationRepository $notificationRepo)
    {
        // Log::info('PushNotifications: start');

        $this->notificationRepository = $notificationRepo;

        $notificationPlanId = (int)array_get($this->options, 'notification_plan_id');
        /** @var NotificationPlan $notificationPlan */
        $this->notificationPlan = NotificationPlan::whereId($notificationPlanId)->first();

        $workspaceId = (int)array_get($this->options, 'workspace_id');
        $templateGroupRestaurants = [];
        if (!empty($workspaceId)) {
            /** @var Workspace $workspace */
            $workspace = Workspace::find($workspaceId);
            if ($workspace) {
                $templateGroupRestaurants = $workspace->groupRestaurants->pluck('id')->toArray();
            }
        }

        // Apply by conditions
        if (array_key_exists('user_id', $this->options)) {
            // Push for only a user
            $data = [];
            $data[] = $this->options;

            $this->applyNotification($data);

            //Send to single user from manager
            if (isset($this->options['notification_plan_id'])) {
                $devices = NotificationDevice::whereUserId($this->options['user_id'])
                    ->whereNotNull('group_restaurant_id')
                    ->whereIn('group_restaurant_id', $templateGroupRestaurants)
                    ->groupBy('user_id')
                    ->get();
                if ($devices) {
                    foreach ($devices as $device) {
                        $additionalData = array_merge($this->options, ['group_restaurant_id' => $device->group_restaurant_id]);
                        if (isset($additionalData['template_id'])) {
                            unset($additionalData['template_id']);
                        }

                        $data = [];
                        $data[] = $additionalData;
                        $this->applyNotification($data);
                    }
                }
            }
        } else {
            // Push for multi user by conditions
            $qUserIds = User::join('notification_devices', 'notification_devices.user_id', '=', 'users.id')
                ->where('users.active', true)
                ->where('notification_devices.active', true)
                ->groupBy('users.id')
                ->select('users.id');

            // Limit to prevent us creating notification plans that aren't needed..
            if (!empty($this->notificationPlan->platform) && $this->notificationPlan->platform == Notification::MANAGER) {
                $notificationPlan = $this->notificationPlan;

                // Manager - Users / Push notifications
                // Only push to template app
                $workspace = Workspace::findOrFail($notificationPlan->workspace_id);
                $groupRestaurants = $workspace->groupRestaurants;

                $qUserIds->where(function ($query) use ($notificationPlan, $groupRestaurants) {
                    $query->where('notification_devices.template_id', $notificationPlan->workspace_id);
                    if ($groupRestaurants->isNotEmpty()) {
                        $query->orWhereIn('notification_devices.group_restaurant_id', $groupRestaurants->pluck('id'));
                    }
                });
            }

            // @todo optimize if we need to send everyone this will take to much time to process and this job will crash..
            // Log::info('PushNotifications: grabbed user ids');

            // Plan for schedule send notification
            if (empty($this->options['send_now'])) {
                if (empty($this->options['types']) && !empty($this->notificationPlan)) {
                    $this->options['types'] = NotificationCategory::where('notification_id', $this->notificationPlan->id)
                        ->pluck('restaurant_category_id')
                        ->toArray();
                }
            }

            // Log::info('PushNotifications: grabbed restaurant category ids');

            // Filter by order
            if (!empty($this->options['types'])) {
                $categoryIds = $this->options['types'];
                $qUserIds->join('orders', 'orders.user_id', '=', 'users.id')
                    ->join('workspace_category', 'workspace_category.workspace_id', 'orders.workspace_id')
                    ->whereIn('workspace_category.restaurant_category_id', $categoryIds);
            }

            // Log::info('PushNotifications: filter by order');

            $qUserIds->chunk(self::LIMIT_USER, function ($users) use ($templateGroupRestaurants) {
                // Log::info('PushNotifications: chunk');

                $data = [];

                // Get all users by conditions
                $userIds = $users->pluck('id')
                    ->toArray();

                $notiDevices = NotificationDevice::whereIn('user_id', $userIds)
                    ->whereIn('group_restaurant_id', $templateGroupRestaurants)
                    ->whereNotNull('group_restaurant_id')
                    ->pluck('group_restaurant_id', 'user_id');

                // Send notification for who register group app
                if (!empty($notiDevices) && $notiDevices->count() > 0) {
                    foreach ($notiDevices as $userId => $groupRestaurantId) {
                        $additionalData = [
                            'user_id' => $userId,
                            'group_restaurant_id' => $groupRestaurantId
                        ];

                        $additionalData = array_merge($this->options, $additionalData);
                        if (isset($additionalData['template_id'])) {
                            unset($additionalData['template_id']);
                        }

                        $data[] = $additionalData;
                    }
                }

                foreach ($userIds as $userId) {
                    $additionalData = [
                        'user_id' => $userId
                    ];

                    $data[] = array_merge($this->options, $additionalData);
                }

                $this->applyNotification($data);
            });

            // When they registered on the web, we can't get device token
            $qUserIdsWithoutDevices = User::join('orders', 'orders.user_id', '=', 'users.id')
                ->leftJoin('notification_devices', 'notification_devices.user_id', '=', 'users.id')
                ->whereNull('notification_devices.id')
                ->where('orders.workspace_id', $workspaceId)
                ->where('users.active', true)
                ->groupBy('users.id')
                ->select('users.id');

            // Push web socket and create new notification records
            $qUserIdsWithoutDevices->chunk(self::LIMIT_USER, function ($users) use ($templateGroupRestaurants) {
                $data = [];

                // Get all users by conditions
                $userIds = $users->pluck('id')
                    ->toArray();

                foreach ($userIds as $userId) {
                    $additionalData = [
                        'user_id' => $userId
                    ];

                    $data[] = array_merge($this->options, $additionalData);
                }

                $this->applyNotification($data);
            });
        }
    }

    /**
     * Apply notification
     *
     * @param array $data
     * @return void Number of success notification was created
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function applyNotification(array $data)
    {
        // Foreach all items to create new notification
        foreach ($data as $item) {
            /** @var Notification $notification */
            $notification = null;

            if ($this->attempts() > 1) {
                $fields = \Schema::getColumnListing(Notification::getTableName());
                $condition = [];

                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        if (isset($item[$field])) {
                            $condition[$field] = array_get($item, $field);
                        }
                    }

                    $notification = Notification::where($condition)
                        ->where('created_at', '>=', now()->subMinutes(8))
                        ->orderBy('created_at', 'DESC')
                        ->first();
                }
            }

            if (is_null($notification)) {
                $notification = $this->notificationRepository->create($item);
            }

            // Log::info('PushNotifications: notification create');

            if (!empty($notification) && !empty($notification->id)) {
                // Push notification case
                // Admin - Users => User web, It's Ready app, Template app
                // Admin - Push notifications => User web, It's Ready app
                // Manager - Users / Push notifications => Template app
                if (!empty($this->notificationPlan)) {
                    // Admin
                    if ($this->notificationPlan->platform == Notification::ADMIN) {
                        // Admin - Users
                        // Admin - Push notifications
                        // Something in here...
                    } // Manager
                    else if ($this->notificationPlan->platform == Notification::MANAGER) {
                        // Manager - Users / Push notifications
                        $userId = $item['user_id'];

                        // Web socket notification from Admin
                        $socket = new Socket();
                        $notificationCondition = [
                            'user_id' => $userId
                        ];

                        if (!empty($notification->group_restaurant_id)) {
                            $notificationCondition['group_restaurant_id'] = $notification->group_restaurant_id;
                        } else {
                            $notificationCondition['template_id'] = $notification->template_id;
                        }

                        $socket->emit('notification', array(
                            'count' => $this->notificationRepository->countUnread($notificationCondition),
                            'userId' => $userId
                        ));
                    }
                }

                // Push notification
                dispatch(new PushNotificationToUser($notification, \App::getLocale()));
            }
        }
    }

}
