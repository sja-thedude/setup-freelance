<?php

namespace App\Jobs;

use App\Models\GroupRestaurant;
use App\Models\GroupRestaurantWorkspace;
use App\Models\Notification;
use App\Models\NotificationDevice;
use App\Models\Workspace;
use App\Services\PushNotification as PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class PushNotificationToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Notification $notification */
    protected $notification;
    protected $locale;
    protected $retryTokens;
    protected $retried;

    /**
     * @var PushNotificationService $pushNotificationService
     */
    protected $pushNotificationService;

    /**
     * Limit item for paging in background process
     */
    const LIMIT_DEVICE = 100;

    /**
     * Create a new job instance.
     *
     * @param Notification $notification
     * @param string|null $locale
     */
    public function __construct(Notification $notification, $locale = null, $retryTokens = [], $retried = 0)
    {
        $this->notification = $notification;
        $this->locale = $locale;
        $this->retryTokens = $retryTokens;
        $this->retried = $retried;

        // Multi-language (locale)
        if (!empty($locale)) {
            \App::setLocale($locale);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;

        /** @var \App\Models\NotificationPlan $notificationPlan */
        $notificationPlan = $this->notification->notificationPlan;
        // Paginate devices to push notification
        $qDevices = NotificationDevice::active()
            ->where('notification_devices.user_id', $this->notification->user_id)
            ->where('notification_devices.notified', true)
            ->select('notification_devices.*');

        if (!empty($this->notification->group_restaurant_id)) {
            $qDevices->where('notification_devices.group_restaurant_id', $this->notification->group_restaurant_id);
        } elseif(!empty($this->notification->template_id)) {
            $qDevices->where('notification_devices.template_id', $this->notification->template_id);
        }

        // Push notification case
        // Admin - Users => User web, It's Ready app, Template app
        // Admin - Push notifications => User web, It's Ready app
        // Manager - Users / Push notifications => Template app
        if (!empty($notificationPlan)) {
            // Admin
            if ($notificationPlan->platform == Notification::ADMIN) {
                // Admin - Users
                // Admin - Push notifications
                $qDevices->whereNull('notification_devices.template_id')
                ->whereNull('notification_devices.group_restaurant_id');
            }
            // Manager
            else if ($notificationPlan->platform == Notification::MANAGER) {
                // Manager - Users / Push notifications
                // Only push to template app
                $workspace = Workspace::findOrFail($notificationPlan->workspace_id);
                $groupRestaurants = false;
                if ($workspace) {
                    $groupRestaurants = $workspace->groupRestaurants;
                }

                $qDevices->where(function ($query) use ($notificationPlan, $groupRestaurants){
                    $query->where('notification_devices.template_id', $notificationPlan->workspace_id);
                    if (!empty($groupRestaurants)) {
                        $query->orWhere(function($q) use ($groupRestaurants) {
                            $q->whereIn('notification_devices.group_restaurant_id', $groupRestaurants->pluck('id'));
                        });
                    }
                });
            }
        }

        $qDevices->chunk(static::LIMIT_DEVICE, function ($devices) {
            $this->pushNotification($devices);
        });
    }

    /**
     * Sending a Downstream Message to Multiple Devices
     *
     * @link https://packagist.org/packages/lkaybob/php-fcm-v1
     * @param Collection $devices
     */
    protected function pushNotification(Collection $devices)
    {
        $tokens = !empty($this->retryTokens) ? $this->retryTokens : $devices->pluck('token')->toArray();
        $title = ! empty($this->notification->title) ? $this->notification->title : '';
        $message = $this->notification->description;
        $workspace = $this->getWorkspaceFromNotification($this->notification);
        // Data for push notification
        $arrData = [
            'action' => 'notification',
            'notification' => $this->notification->getPushNotificationMetaData(),
        ];

        $fcmHttpConfigs = config('fcm.http');
        $config = $this->getFcmConfig();
        $fcmHttpConfigs = array_merge($fcmHttpConfigs, $config);
        config(['fcm.http' => $fcmHttpConfigs]);

        $downstreamResponse = $this->pushNotificationService->pushNotification($tokens, $title, $message, $arrData, $workspace);

        // return Array - you must remove all this tokens in your database
        $tokensToDelete = $downstreamResponse->tokensToDelete();
        // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
        $tokensWithError = $downstreamResponse->tokensWithError();
        $tokenNeedRemove = array_merge($tokensToDelete, !empty($tokensWithError) ? array_keys($tokensWithError) : []);
        // Remove token in database
        if(!empty($tokenNeedRemove)) {
            NotificationDevice::whereIn('token', $tokenNeedRemove)->update([
                'active' => 0
            ]);
        }

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $tokensToModify = $downstreamResponse->tokensToModify();
        // Update new token into database
        if(!empty($tokensToModify)) {
            foreach ($tokensToModify as $oldToken => $newToken) {
                NotificationDevice::where('token', $oldToken)->update(['token' => $newToken]);
            }
        }

        // return Array - you should try to resend the message to the tokens in the array
        $tokensToRetry = $downstreamResponse->tokensToRetry();
        // Call again to solve the tokens need retry
        if(!empty($tokensToRetry) && $this->retried <= 3) {
            dispatch(new \App\Jobs\PushNotificationToUser($this->notification, $this->locale, $tokensToRetry, ($this->retried + 1)));
        }
    }

    /**
     * Get config for firebase
     *
     * @return array
     */
    protected function getFcmConfig()
    {
        $templateId = $this->notification->template_id;
        $groupRestaurantId = $this->notification->group_restaurant_id;
        $fcmProjects = config('fcm.projects');
        $config = [
            'project_id' => config('fcm.default_project.project_id'),
            'private_file' => config('fcm.default_project.private_file'),
            'sender_id' => config('fcm.default_project.sender_id'),
            'server_key' => config('fcm.default_project.server_key')
        ];

        $firebaseProject = null;
        if (!empty($templateId)) {
            $workspace = Workspace::find($templateId);
            if ($workspace) {
                $firebaseProject = $workspace->firebase_project;
            }
        }

        if (!empty($groupRestaurantId)) {
            $groupRestaurant = GroupRestaurant::find($groupRestaurantId);
            if ($groupRestaurant) {
                $firebaseProject = $groupRestaurant->firebase_project;
            }
        }

        if (!is_null($firebaseProject)) {
            $config = [
                'project_id' => $fcmProjects[$firebaseProject]['project_id'],
                'private_file' => $fcmProjects[$firebaseProject]['private_file'],
                'sender_id' => $fcmProjects[$firebaseProject]['sender_id'],
                'server_key' => $fcmProjects[$firebaseProject]['server_key']
            ];
        }

        return $config;
    }

    /**
     * Get the workspace from the notification
     *
     * @return Workspace|null
     */
    private function getWorkspaceFromNotification(Notification $notification)
    {
        $workspace = $notification->template;

        if (empty($workspace) && !empty($notification->notificationPlan)) {
            $workspace = $notification->notificationPlan->workspace;
        }

        if (empty($workspace) && !empty($notification->group_restaurant_id)) {
            $grWorkspaces = GroupRestaurantWorkspace::where('group_restaurant_id', $notification->group_restaurant_id)
                ->get();

            /** @var GroupRestaurantWorkspace $grWorkspace */
            $grWorkspace = $grWorkspaces->first();

            if (!empty($grWorkspace)) {
                $workspace = $grWorkspace->workspace;
            }
        }

        return $workspace;
    }
}
