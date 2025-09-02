<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\SettingPayment;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ConfirmOrderToManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:confirm_order_to_manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var OrderRepository $orderRepository
     */
    protected $orderRepository;

    const LIMIT_ORDER = 20;

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
     * @param OrderRepository $orderRepo
     * @return mixed
     */
    public function handle(OrderRepository $orderRepo)
    {
        $this->orderRepository = $orderRepo;
        $today = Carbon::today();

        Order::whereDate('date_time', $today)
            ->where('email_confirmations_manager', false)
            // Filter order by payment status
            ->where(function ($paymentInfo) {
                /** @var \Illuminate\Database\Eloquent\Builder $paymentInfo */
                $paymentInfo
                    // Payment method online with status is paid
                    ->whereIn('orders.status', [Order::PAYMENT_STATUS_PAID])
                    // Or use payment method is cash / invoice
                    ->orWhereIn('orders.payment_method', [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR]);
            })
            ->select('orders.*')
            ->with(['workspace', 'workspace.user'])
            ->chunk(static::LIMIT_ORDER, function ($orders) use ($today) {
                foreach ($orders as $order) {
                    /** @var \App\Models\Order $order */

                    $this->orderRepository->sendMailConfirm($order, $order->workspace->user);
                }

                // Mark as email confirmations to manager of restaurant
                $orderIds = $orders->pluck('id')
                    ->toArray();
                \DB::table('orders')
                    ->whereIn('id', $orderIds)
                    ->update([
                        'email_confirmations_manager' => true
                    ]);
            });
    }
}
