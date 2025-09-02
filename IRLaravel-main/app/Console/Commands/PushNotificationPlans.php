<?php

namespace App\Console\Commands;

use App\Models\NotificationPlan;
use App\Repositories\NotificationRepository;
use Illuminate\Console\Command;

class PushNotificationPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:push_notification_plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push notification plans';

    protected $notificationRepository;

    const LIMIT_PLAN = 100;

    /**
     * Create a new command instance.
     *
     * @param NotificationRepository $notificationRepo
     */
    public function __construct(NotificationRepository $notificationRepo)
    {
        parent::__construct();

        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        NotificationPlan::where('send_now', false)
            ->where('send_datetime', '<=', \Carbon\Carbon::now())
            ->where('is_sent', false)
            ->chunk(static::LIMIT_PLAN, function ($plans) {
                $ids = [];

                /** @var \App\Models\NotificationPlan $plan */
                foreach ($plans as $plan) {
                    $ids[] = $plan->id;
                    $attributes = $plan->toArray();

                    // Clear ID
                    unset($attributes['id']);
                    // Push reference id
                    $attributes['notification_plan_id'] = $plan->id;
                    $attributes['template_id'] = $plan->workspace_id;

                    // Apply push notification
                    $this->notificationRepository->applyNotification($attributes);
                }

                // Mark is send plans
                NotificationPlan::whereIn('id', $ids)
                    ->update([
                        'is_sent' => true,
                    ]);
            });
    }
}
