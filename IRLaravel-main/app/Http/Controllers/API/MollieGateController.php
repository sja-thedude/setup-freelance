<?php

namespace App\Http\Controllers\API;

use App\Facades\Helper;
use App\Models\Order;
use App\Models\SettingPayment;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingPaymentRepository;
use Illuminate\Http\Request;
use Log;
use Mollie\Api\MollieApiClient;

class MollieGateController extends AppBaseController
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transaction(Request $request)
    {
        try {
            \DB::beginTransaction();

            $orderId = $request->get('order_id');

            /** @var \App\Models\Order|null $order */
            $order = $this->orderRepository->findWhere(['id' => $orderId])->first();

            /** @var \App\Models\SettingPayment|null $settingPayment */
            $settingPayment = $this->settingPaymentRepository->findWhere([
                'workspace_id' => $order->workspace_id,
                'type'         => SettingPayment::TYPE_MOLLIE,
            ])->first();

            // Invalid order
            if (empty($order)) {
                return $this->sendError(trans('order.not_found'));
            }

            // Invalid API token
            if (empty($settingPayment->api_token)) {
                return $this->sendError(trans('setting_payment.message_invalid_token'));
            }

            // Setup Mollie token
            $this->mollie->setApiKey($settingPayment->api_token);

            // Mollie redirect to current system with params:
            $redirectParams = [
                'orderId' => $orderId,
                'order_id' => $orderId,
                'workspaceId' => $order->workspace_id,
                'is_api' => 1,
                'group_id' => $order->group_id ?? null,
                'origin' => $request->get('origin', 'app')
            ];

            // Get App-Token from request
            $appToken = Helper::getAppToken($request);

            if (!empty($appToken)) {
                $redirectParams['App-Token'] = $appToken;
            }

            //Get Group-Token from Request
            $groupToken = Helper::getGroupToken($request);
            if (!empty($groupToken)) {
                $redirectParams['Group-Token'] = $groupToken;
            }

            $userLoggedIn = $this->orderRepository->getJWTAuth();
            $metaDataUser = [];

            if ($userLoggedIn) {
                $metaDataUser = $userLoggedIn->only('name', 'gsm', 'email');
            }

            $cancelUrl = route('web.error', $redirectParams);

            // Current domain
            $domain = url('/');
            // Get referer domain
            $domainReferer = $request->server('HTTP_REFERER');
            $domainDiff = false;
            $domainRedirect = $domain;

            if (!empty($domainReferer) && $domainReferer != $domain) {
                $domainDiff = true;
                $domainRedirect = rtrim($domainReferer, '/');
            }

            if ($domainDiff) {
                $metaDataUser['referer'] = $domainRedirect;
                $cancelUrl = str_replace($domain, $domainRedirect, $cancelUrl);
            }

            // Set current request url
            if ($request->has('current_url')) {
                $metaDataUser['current_url'] = $request->get('current_url');
            }

            $totalPrice = $order->total_price;

            if($request->get('origin', 'app') == 'next') {
                $redirectParams['next_cancel_url'] = $cancelUrl;
                $cancelUrl = route('api.mollie.callback', $redirectParams);
            }

            $payment = $this->mollie->payments->create([
                "method"      => NULL,
                "amount"      => [
                    "currency" => "EUR",
                    "value"    => $totalPrice
                ],
                "description" => isset($userLoggedIn->name)?"Order #" . $order->id . ' ' . $userLoggedIn->name:"Order #" . $order->id,
                "redirectUrl" => route('api.mollie.callback', $redirectParams),
                "webhookUrl"  => route('api.mollie.webhook', $redirectParams),
                "cancelUrl" => $cancelUrl,
                "metadata" => $metaDataUser
            ]);

            $order->update(['mollie_id' => $payment->id]);

            \DB::commit();

            return $this->sendResponse([
                'payment_id' => $payment->id,
                'url'        => $payment->getCheckoutUrl(),
            ], trans('messages.success'));
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error($e->getMessage());

            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;
            return $this->sendError($e->getMessage(), $errorCode);
        }
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request, $orderId)
    {
        try {
            /** @var \App\Models\Order|null $order */
            $order = $this->orderRepository
                ->with(['settingPayment', 'workspace'])
                ->findWhere(['id' => $orderId])
                ->first();

            // Invalid order
            if (empty($order)) {
                return $this->sendError(trans('order.not_found'));
            }

            $this->mollie->setApiKey($order->settingPayment->api_token);
            $payment = $this->mollie->payments->get($order->mollie_id);

            $domain = url('/');
            $subDomain = $order->workspace->slug . "." . config('app.domain');
            $subDomainFull = parse_url($domain, PHP_URL_SCHEME) . '://' . $subDomain;
            $url = str_replace(config('app.domain'), $subDomain, route('web.orders.show', $order->id));
            $errorParams = array_merge($request->all(), [
                'workspaceId' => $order->workspace_id,
                'is_api' => 1,
                'order_id' => $orderId,
                'origin' => $request->get('origin', 'app')
            ]);

            if ($payment->isCanceled() || $payment->isFailed() || $payment->isExpired()) {
                $url = $payment->cancelUrl ?? route('web.error', $errorParams);
                $this->rollbackOrUpdateLoyalty($order, 'rollback');
            } else if ($payment->isOpen()) {
                $url = $payment->metadata->current_url ?? route('web.error', $errorParams);
                $this->rollbackOrUpdateLoyalty($order, 'rollback');
            } else {
                \DB::beginTransaction();

                if ($payment->paidAt) {
                    $this->rollbackOrUpdateLoyalty($order, 'update');
                    $order = $this->isPaidProcess($order, $payment);
                }

                \DB::commit();
            }

            $redirectParams = [
                'is_api' => 1,
                'group_id' => $order->group_id ?? null,
                'origin' => $request->get('origin', 'app')
            ];

            if (!empty($order)) {
                $redirectParams['order_id'] = $order->id;
            }

            // Get App-Token from request
            $appToken = Helper::getAppToken($request);

            if (!empty($appToken)) {
                $redirectParams['App-Token'] = $appToken;
            }

            //Get Group-Token from Request
            $groupToken = Helper::getGroupToken($request);
            if (!empty($groupToken)) {
                $redirectParams['Group-Token'] = $groupToken;
            }

            $url .= str_contains($url, '?') ? '&' : '?' . http_build_query($redirectParams);

            if (!empty($payment->metadata->referer)) {
                $url = str_replace($subDomainFull, $payment->metadata->referer, $url);

                if (!str_contains($url, $subDomainFull)) {
                    $url = str_replace($domain, $payment->metadata->referer, $url);
                }
            }

            if(($payment->isCanceled() || $payment->isFailed() || $payment->isExpired() || $payment->isOpen())
                && !is_null($request->get('next_cancel_url', null))) {
                return redirect($request->get('next_cancel_url', null));
            }

            return redirect($url);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error($e->getMessage());

            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;
            return $this->sendError($e->getMessage(), $errorCode);
        }
    }

    /**
     * @param Request $request
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function webhook(Request $request, $orderId)
    {
        try {
            if (!$request->has('id')) {
                return $this->sendError(trans('order.mollie_message_invalid_payment_id'));
            }

            $paymentId = $request->get('id');

            \DB::beginTransaction();

            /** @var \App\Models\Order|null $order */
            $order = $this->orderRepository
                ->with(['settingPayment', 'workspace'])
                ->findWhere([
                    'id' => $orderId,
                    'mollie_id' => $paymentId,
                ])
                ->first();

            // Invalid order
            if (empty($order)) {
                return $this->sendError(trans('order.not_found'));
            }

            //Prevent Mollie updating status and sending confirmation email for orders which are already PAID
            if ($order->status == Order::PAYMENT_STATUS_PAID) {
                return $this->sendError(trans('order.already_paid'));
            }

            /** @var \App\Models\SettingPayment|null $settingPayment */
            $settingPayment = $order->settingPayment;
            $this->mollie->setApiKey($settingPayment->api_token);
            $payment = $this->mollie->payments->get($paymentId);

            // Paid
            if ($payment->isPaid()) {
                $this->rollbackOrUpdateLoyalty($order, 'update');
                $this->isPaidProcess($order, $payment);
                // Remove the cart
                if(!empty($payment->metadata) && !empty($payment->metadata->cartId)) {
                    $cartId = $payment->metadata->cartId;
                    $this->cartRepository->deleteWhere(['id' => $cartId]);
                }
            }
            // Canceled
            else if ($payment->isCanceled()) {
                $order->status = Order::PAYMENT_STATUS_CANCELLED;
                $order->save();
            }
            // Failed
            else if ($payment->isFailed()) {
                $order->status = Order::PAYMENT_STATUS_FAILED;
                $order->save();
            }
            // Expired
            else if ($payment->isExpired()) {
                $order->status = Order::PAYMENT_STATUS_EXPIRED;
                $order->save();
            }
            // Unknown
            else {
                $order->status = Order::PAYMENT_STATUS_UNKOWN;
                $order->save();
            }

            if ($order->status != Order::PAYMENT_STATUS_PAID) {
                $this->rollbackOrUpdateLoyalty($order, 'rollback');
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error($e->getMessage());

            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;
            return $this->sendError($e->getMessage(), $errorCode);
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
        if($this->orderRepository->confirmedOrderSendMailAndPrint($order, !empty($order->email_confirmations_manager))) {
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

    protected function rollbackOrUpdateLoyalty($order, $type = 'rollback') {
        if($type == 'rollback') {
            $this->orderRepository->restoreLastRedeem($order);

            if(!empty($order->loyalty_added)) {
                $this->orderRepository->rollbackLoyalty($order);
            }
        } else {
            $this->orderRepository->incrementLoyalty($order);
        }
    }
}
