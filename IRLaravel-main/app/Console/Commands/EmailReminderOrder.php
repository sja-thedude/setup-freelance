<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Jobs\SendEmailReminderOrder;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Helpers\Order as OrderHelper;

class EmailReminderOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:reminder_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send remainder order';

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
        $orders = Order::with([
            'user',
            'group',
            'orderItems',
            'workspace.settingPreference',
            'workspace.settingDeliveryConditions',
        ])
            // Only before 10 minutes
            ->where('date_time', '>=', \Carbon\Carbon::now()->subMinutes(10))
            // Only get individual order or group order items
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
            ->get();

        $currentLang = app()->getLocale();

        foreach ($orders as $k => $order) {
            if (!$order->date || !$order->workspace || !$order->user) {
                continue;
            }

            $order = OrderHelper::sortOptionItems($order);

            $minutes            = 0;
            $user               = $order->user;
            $workspace          = $order->workspace;
            $lang               = $user ? $user->getLocale() : $workspace->language;
            $isMailActive       = FALSE;
            $dateTimeLocal      = Helper::convertDateTimeToTimezone($order->date . " " . $order->time, $order->timezone);
            $dateTimeLocalParse = Carbon::parse($dateTimeLocal);
            $timeLocal          = $dateTimeLocalParse->format("H:i");

            app()->setLocale($lang);
            $codeId = "#" . ($order->group_id ? "G" . $order->parent_code : $order->code) . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '');
            $dataContent = [
                'isSendMail'            => TRUE,
                'isDeleveringAvailable' => TRUE,
                'isDeleveringPriceMin'  => TRUE,
                'cart'                  => $order,
                'listItem'              => $order->orderItems,
                'conditionDelevering'   => $workspace->settingDeliveryConditions->first(),
                'totalPrice'            => 0,
                'content1'              => trans('mail.reminder.content1'),
                'content4'              => trans('mail.reminder.content4'),
                'content5'              => trans('mail.reminder.content5'),
                'content6'              => trans('cart.totaal'),
                'content7'              => trans('cart.success_betaalstatus'),
                'content8'              => trans('cart.success_betaalmethode'),
                'content9'              => trans('cart.success_opmerkingen'),
                'content10'             => trans('cart.groep'),
                'content11'             => trans('cart.levering_op_adres'),
                'content2'              => trans('mail.reminder.content2', [
                    'first_name' => $user->first_name,
                ]),
                'content3'              => trans('mail.reminder.content3', [
                    'restaurant' => $workspace->name,
                    'order_id'   => $codeId,
                    'time'       => $timeLocal,
                ]),
                'content12'             => trans('mail.reminder.content12', [
                    'restaurant' => $workspace->name,
                    'order_id'   => $codeId,
                    'time'       => $timeLocal,
                ]),
            ];
            
            //Is test account
            $dataContent['content_note'] = "";
            if($order->is_test_account) {
                $dataContent['content2'] = trans('mail.reminder.content2', [
                    'first_name' => strtoupper(trans('strings.admin')),
                ]);
                $dataContent['content_note'] = trans('mail.reminder.content_note');
            }

            if ($workspace->settingPreference && $workspace->settingPreference->use_email) {
                $isMailActive = TRUE;
                $minutes = $workspace->settingPreference->mins_before_notify ?: 0;
            }

            $dateTimeOrder = Carbon::parse($order->gereed)
                ->addMinutes(-$minutes)
                ->format("Y-m-d H:i");

            if ($isMailActive && $user->email && Carbon::now()->format("Y-m-d H:i") === $dateTimeOrder) {
                SendEmailReminderOrder::dispatch($user, $dataContent);
            }
        }

        app()->setLocale($currentLang);
    }
}
