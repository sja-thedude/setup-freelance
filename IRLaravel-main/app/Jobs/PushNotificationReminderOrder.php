<?php

namespace App\Jobs;

use App\Facades\Helper;
use App\Models\Order;
use App\Repositories\NotificationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class PushNotificationReminderOrder
 * @package App\Jobs
 */
class PushNotificationReminderOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var NotificationRepository $notificationRepository
     */
    protected $notificationRepository;

    /**
     * @var array $orderIds
     */
    protected $orderIds;

    /**
     * Create a new job instance.
     *
     * @param array $orderIds
     * @param string|null $locale
     */
    public function __construct(array $orderIds, $locale = null)
    {
        $this->orderIds = $orderIds;

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
     */
    public function handle(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;

        // Invalid order id list
        if (empty($this->orderIds)) {
            return;
        }

        /** @var \Illuminate\Support\Collection $orders */
        $orders = Order::whereIn('id', $this->orderIds)
            ->with(['user', 'workspace'])
            ->get();

        $orders->each(function ($order) {
            /** @var \App\Models\Order $order */

            $user = $order->user;
            if ($user) {
                $workspace = $order->workspace;
                $timezone = (!empty($order->timezone)) ? $order->timezone : config('app.timezone');
    
                // Order code
                $orderCode = $order->code;
    
                // Order code with prefix G for Group
                if (!empty($order->group_id)) {
                    $orderCode = 'G' . $orderCode . (!empty($order->extra_code) ? '-' . $order->extra_code : '');
                }
    
                $notificationAttributes = [
                    'title' => trans('notification.push_notification_reminder_order_title', [
                        'order_code' => $orderCode,
                    ]),
                    'description' => trans('notification.push_notification_reminder_order_content', [
                        'first_name' => $user->first_name,
                        'restaurant_name' => $workspace->name,
                        'order_code' => $orderCode,
                        'date_time' => Helper::getDatetimeFromFormat($order->date_time, null, null, $timezone),
                    ]),
                    'sent_time' => \Carbon\Carbon::now()->toDateTimeString(),
                    'user_id' => $user->id,
                    'template_id' => $order->template_id,
                    'group_restaurant_id' => $order->group_restaurant_id,
                ];
    
                $this->notificationRepository->applyNotification($notificationAttributes);
            }
        });

        // Mark as push notification
        Order::whereIn('id', $this->orderIds)
            ->update([
                'push_notification_reminder' => true,
            ]);
    }
}
