<?php

use Illuminate\Database\Seeder;

use App\Models\Order;
use App\Models\SettingPayment;
use App\Repositories\OrderRepository;

class OrderTotalPaidTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::where('status', Order::PAYMENT_STATUS_PAID)
            ->whereNotNull('mollie_id')
            ->whereNotNull('total_price')
            ->whereNull('total_paid')
            ->where('payment_method', SettingPayment::TYPE_MOLLIE)
            ->chunk(50, function ($orders){
                foreach($orders as $order) {
                    if(empty($order->payed_at)) {
                        $order->payed_at = $order->created_at;
                    }

                    $order->total_paid = $order->total_price;
                    $order->save();

                    // Make paid order items
                    $orderRepository = new OrderRepository(app());
                    $orderRepository->makePaidOrderItems($order);
                }
            });

        Order::where('status', Order::PAYMENT_STATUS_PAID)
            ->whereNotNull('mollie_id')
            ->whereNull('payed_at')
            ->where('payment_method', SettingPayment::TYPE_MOLLIE)
            ->chunk(50, function ($orders){
                foreach($orders as $order) {
                    $order->payed_at = $order->created_at;
                    $order->save();
                }
            });
    }
}
