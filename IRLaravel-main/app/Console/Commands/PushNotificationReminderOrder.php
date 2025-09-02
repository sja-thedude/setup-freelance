<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use Illuminate\Console\Command;

class PushNotificationReminderOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:push_notification_reminder_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push notification to reminder order';

    const LIMIT_WORKSPACE = 20;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Current timestamp
        $timestamp = \Carbon\Carbon::now()->toDateTimeString();

        Workspace::join('setting_preferences', 'setting_preferences.workspace_id', '=', 'workspaces.id')
            ->where('setting_preferences.receive_notify', true)
            ->active()
            ->with(['settingPreference'])
            ->select('workspaces.*')
            ->chunk(static::LIMIT_WORKSPACE, function ($workspaces) use ($timestamp) {
                $workspaces->each(function ($workspace) use ($timestamp) {
                    /** @var \App\Models\Workspace $workspace */

                    $settingPreference = $workspace->settingPreference;
                    $minBeforeNotify = (int)$settingPreference->mins_before_notify;
                    $minBeforeNotifyRanger = (int)($minBeforeNotify - 10);

                    // Get all orders in the workspace
                    $orderIds = $workspace->orders()
                        ->where('push_notification_reminder', false)
                        ->whereRaw("(date_time - INTERVAL {$minBeforeNotify} MINUTE <= '{$timestamp}' 
                                    AND date_time - INTERVAL {$minBeforeNotifyRanger} MINUTE >= '{$timestamp}')")
                        ->where(function ($individualOrder) {
                            /** @var \Illuminate\Database\Eloquent\Builder $individualOrder */
                            $individualOrder->whereNull('orders.group_id')
                                ->orWhere(function ($groupOrder) {
                                    /** @var \Illuminate\Database\Eloquent\Builder $groupOrder */
                                    $groupOrder->whereNotNull('orders.group_id')
                                        ->whereNotNull('orders.parent_id');
                                });
                        })
                        // Filter order by payment status
                        ->where(function ($paymentInfo) {
                            /** @var \Illuminate\Database\Eloquent\Builder $paymentInfo */
                            $paymentInfo
                                // Payment method online with status is paid
                                ->where('orders.status', \App\Models\Order::PAYMENT_STATUS_PAID)
                                // Or use payment method is cash / invoice
                                ->orWhereIn('orders.payment_method', [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]);
                        })
                        ->pluck('orders.id')
                        ->toArray();

                    // Get locale from workspace
                    $locale = (!empty($workspace->language)) ? $workspace->language : \App::getLocale();

                    // Push notification
                    dispatch(new \App\Jobs\PushNotificationReminderOrder($orderIds, $locale));
                });
            });
    }
}
