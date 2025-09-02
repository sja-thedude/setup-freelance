<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Order;
use App\Models\SettingPayment;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingPaymentRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;
use Log;
use Mollie\Api\MollieApiClient;

class MollieGateController extends BaseController
{
    /**
     * @var
     */
    private $cartRepository;

    /**
     * @var
     */
    private $orderRepository;

    /**
     * @var
     */
    private $workspaceRepository;

    /**
     * @var
     */
    private $settingPaymentRepository;

    /**
     * @var
     */
    private $mollie;

    /**
     * MollieGateController constructor.
     *
     * @param CartRepository           $cartRepository
     * @param OrderRepository          $orderRepository
     * @param WorkspaceRepository      $workspaceRepository
     * @param SettingPaymentRepository $settingPaymentRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        WorkspaceRepository $workspaceRepository,
        SettingPaymentRepository $settingPaymentRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->settingPaymentRepository = $settingPaymentRepository;

        $this->mollie = new MollieApiClient();

        parent::__construct();
    }

    /**
     * @param Request $request
     * @param         $cartId
     * @param         $oldUrl
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function index(Request $request, $cartId, $oldUrl, $urlStep2 = NULL)
    {
        $oldUrl = decrypt($oldUrl);
        if (!empty($urlStep2)) {
            $urlStep2 = decrypt($urlStep2);
        }
        $cart = \App\Models\Cart::with(['cartItems', 'cartItems.cartOptionItems', 'cartItems.cartOptionItems.optionItem'])->findOrFail($cartId);

        if (!$cart) {
            return redirect($oldUrl);
        }

        $settingPayment = $this->settingPaymentRepository->findWhere([
            'workspace_id' => $cart->workspace_id,
            'type'         => SettingPayment::TYPE_MOLLIE,
        ])->first();

        $this->mollie->setApiKey($settingPayment->api_token);

        $redirectParams = [];

        // Order from a restaurant website
        if ($request->has('template_id')) {
            $redirectParams['template_id'] = (int)$request->get('template_id');
        }

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $metaDataUser = [];
        if ($user) {
            $metaDataUser = $user->only('name', 'gsm', 'email');
        }

        $metaDataUser['cartId'] = $cart->id;

        \DB::beginTransaction();

        $condition = $this->workspaceRepository->getDeliveryConditions($cart->workspace_id, [
            'lat'  => $cart->lat,
            'lng'  => $cart->long,
        ])->first();

        $data = Helper::createDataOrder($cart, SettingPayment::TYPE_MOLLIE, $condition);

        // Make default is pending
        $data['status'] = Order::PAYMENT_STATUS_PENDING;

        // Order from a restaurant website
        if ($request->has('template_id')) {
            $data['template_id'] = (int)$request->get('template_id');
        }

        // Create new order
        /** @var \App\Models\Order|null $order */
        $order = $this->orderRepository->create($data);

        // Redirect with order
        $redirectParams['orderId'] = $order->id;

        \DB::commit();

        try {
            $payment = $this->mollie->payments->create([
                "method"      => NULL,
                "amount"      => [
                    "currency" => "EUR",
                    "value"    => $order->total_price
                ],
                "description" => isset($user->name)?"Order #" . $order->id . ' ' . $user->name:"Order #" . $order->id,
                "redirectUrl" => route('web.mollie.redirect', $redirectParams),
                "webhookUrl"  => route('api.mollie.webhook', $redirectParams),
                "metadata" => $metaDataUser
            ]);
        } catch (\Exception $exception) {
            session(['not_response_mollie' => true]);
            return redirect($urlStep2);
        }

        // Push mollie_id to record model
        $order->update(['mollie_id' => $payment->id]);

        session([
            'paymentId'      => $payment->id,
            'cart'           => $cart,
            'oldUrl'         => $oldUrl,
        ]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    /**
     * Mollie callback to the system
     *
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
    1 */
    public function redirect(Request $request, $orderId)
    {
        $oldUrl = session()->get('oldUrl');

        try {
            \DB::beginTransaction();

            /** @var \App\Models\Order|null $order */
            $order = $this->orderRepository
                ->with(['settingPayment', 'workspace'])
                ->findWhere(['id' => $orderId])
                ->first();

            // Invalid order
            if (empty($order)) {
                return redirect($oldUrl);
            }

            $this->mollie->setApiKey($order->settingPayment->api_token);
            $payment = $this->mollie->payments->get($order->mollie_id);

            if ($payment->paidAt) {
                $order = $this->isPaidProcess($order, $payment);
                // Order from a restaurant website
                if ($request->has('template_id')) {
                    $data['template_id'] = (int)$request->get('template_id');
                }

                \DB::commit();
                return redirect(route('web.orders.show', $order->id));
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error($e->getMessage());
        }

        return redirect($oldUrl);
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

    /**
     * @param Order $order
     * @return bool
     */
    protected function confirmedOrderSendMailAndPrint(Order $order) {
        $order = $order->refresh();

        // Make sure we only do this if the order is paid
        if($order->status != Order::PAYMENT_STATUS_PAID) {
            return false;
        }

        // Confirmed order
        if($this->orderRepository->confirmedOrderSendMailAndPrint($order)) {
            return true;
        }

        return false;
    }

    protected function setFalsePrintParent($order) {
        if(!empty($order->group_id) && !empty($order->parent_id)) {
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
