<?php

namespace App\Console\Commands;

use App\Models\SettingPrint;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingPaymentRepository;
use Mollie\Api\MollieApiClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Order;

/**
 * The RestoreMissingOrder class.
 */
class RestoreMissingOrder extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $signature = 'order:restore-missing {orderId}';

    /**
     * MollieGateController constructor.
     *
     * @param CartRepository           $cartRepository
     * @param OrderRepository          $orderRepository
     * @param SettingPaymentRepository $settingPaymentRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        SettingPaymentRepository $settingPaymentRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->settingPaymentRepository = $settingPaymentRepository;
        $this->mollie = new MollieApiClient();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::beginTransaction();

        try {
            if(!empty($this->argument('orderId'))) {
                $orderId = $this->argument('orderId');
                $order = Order::findOrFail($orderId);

                if(!empty($order)) {
                    if (!empty($order->mollie_id) && $order->status == Order::PAYMENT_STATUS_PAID) {
                        \DB::commit();
                        return false;
                    }

                    $this->mollie->setApiKey($order->settingPayment->api_token);
                    $payment = $this->mollie->payments->get($order->mollie_id);

                    if ($payment->paidAt) {
                        $this->orderRepository->incrementLoyalty($order);
                        $this->isPaidProcess($order, $payment);

                        \DB::commit();
                    }
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
        }
    }

    protected function isPaidProcess($order, $payment) {
        if(empty($order->trigger_auto_scan)) {
            $this->setFalsePrintParent($order);
            $order->status = Order::PAYMENT_STATUS_PAID;
            $order->total_paid = $order->total_price;
            $order->payed_at = $payment->paidAt;
            $order->trigger_auto_scan = true;
            $order->save();
            $order = $order->refresh();
            // Make paid order items
            $this->orderRepository->makePaidOrderItems($order);
            $this->confirmedOrderSendMailAndPrint($order);
        }

        return $order;
    }

    protected function confirmedOrderSendMailAndPrint(Order $order) {
        $today = Carbon::today();

        $orderId = $order->id;
        if((!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) && !empty($order->parent_id)) {
            $orderId = $order->parent_id;
        }

        $order = $order->refresh();

        // Make sure we only do this if the order is paid
        if($order->status != Order::PAYMENT_STATUS_PAID) {
            return false;
        }
        $locale = $order->contact ? $order->contact->locale : ($order->user ? $order->user->getLocale() : $order->workspace->language);
        // Do printing
        \App\Facades\Order::autoPrintOrder(
            [$orderId],
            [SettingPrint::TYPE_WERKBON, SettingPrint::TYPE_KASSABON, SettingPrint::TYPE_STICKER],
            false,
            true,
            null,
            $locale
        );

        // Send mail order success
        $this->orderRepository->sendMailConfirm($order);

        // Send order confirmation to manager as well.
        // In case they have internet breakdown they still have access to the order confirmation.
        // If the order is created for today, the email is sent immediately.
        // If the order is created for days in the future, the email is sent at 00:15 on that date.
        if ($order->date_time->toDateString() == $today->toDateString()) {
            $this->orderRepository->sendMailConfirm($order, $order->workspace->user);

            // Mark as email confirmations to manager of restaurant
            $orderIds = [$order->id];
            \DB::table('orders')
                ->whereIn('id', $orderIds)
                ->update([
                    'email_confirmations_manager' => true
                ]);
        }

        return true;
    }

    protected function setFalsePrintParent($order) {
        if((!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) && !empty($order->parent_id)) {
            Order::where('id', $order->parent_id)
                ->update([
                    'printed' => false,
                    'printed_a4' => false,
                    'printed_sticker' => false,
                    'printed_kassabon' => false,
                    'printed_werkbon' => false,
                    'auto_print' => false,
                    'auto_print_sticker' => false,
                    'auto_print_werkbon' => false,
                    'auto_print_kassabon' => false,
                    'printed_sticker_multi' => false
                ]);
        }
    }
}
