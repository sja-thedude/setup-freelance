<?php

namespace App\Jobs\PushOrderToConnectors;

use App\Helpers\Helper;
use App\Helpers\Order as OrderHelper;
use App\Jobs\PushOrderToConnectors;
use App\Models\Email;
use App\Models\Order;
use App\Models\OrderReference;
use App\Models\ProductReference;
use App\Models\SettingConnector;
use App\Models\SettingOpenHour;
use App\Models\SettingOpenHourReference;
use App\Models\SettingPayment;
use App\Models\SettingPaymentReference;
use App\Models\Workspace;
use App\Repositories\OptionItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingConnectorRepository;
use App\Services\Connector\HendrickxKassaConnector;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class HendrickxKassas
 * @package App\Jobs
 */
class HendrickxKassas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * @var int
     */
    protected $connectorId;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var null
     */
    protected $triggeredType = null;

    /**
     * @var null|OrderReference
     */
    protected $orderReference = null;

    /**
     * Create a new job instance.
     *
     * @param $connectorId
     * @param $orderId
     * @param $triggerType
     */
    public function __construct(
        $connectorId,
        $orderId,
        $triggerType
    ) {
        $this->connectorId = $connectorId;
        $this->orderId = $orderId;
        $this->triggeredType = $triggerType;
    }

    /**
     * Execute the job.
     *
     * @param SettingConnectorRepository $settingConnectorRepository
     * @param OrderRepository $orderRepository
     * @param ProductRepository $productRepository
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(
        SettingConnectorRepository $settingConnectorRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        OptionItemRepository $optionItemRepository
    ) {
        /** @var SettingConnector|null|false $connector */
        $settingConnector = $settingConnectorRepository->find((int) $this->connectorId);
        if(empty($settingConnector)) {
            $error = 'PushOrderToConnectors\HendrickxKassas: could not find connector ID ' . ((int) $this->connectorId);
            Log::error($error);
            $exception = new \Exception($error);
            $this->tryToRecover($exception);
            return;
        }

        /** @var Order|null|false $order */
        $order = $orderRepository->find((int) $this->orderId);
        if(empty($order)) {
            $error = 'PushOrderToConnectors\HendrickxKassas: could not find order ID ' . ((int) $this->orderId);
            Log::error($error);
            $exception = new \Exception($error);
            $this->tryToRecover($exception);
            return;
        }

        /** @var Workspace|null|false $workspace */
        $workspace = $order->workspace;
        if(empty($workspace)) {
            $error = 'PushOrderToConnectors\HendrickxKassas: could not find order (' . ((int) $this->orderId) . ') -> workspace';
            Log::error($error);
            $exception = new \Exception($error);
            $this->tryToRecover($exception);
            return;
        }

        // Get order reference..
        $this->orderReference = $order->orderReferences()
            ->where('provider', $settingConnector->provider)
            ->where('local_id', $this->orderId)
            ->first();

        if(empty($this->orderReference)) {
            $orderReferenceNeedsSave = false;

            $this->orderReference = new OrderReference();
            $this->orderReference->workspace_id = $workspace->id;
            $this->orderReference->local_id = $order->id;
            $this->orderReference->provider = $settingConnector->provider;

            switch($this->triggeredType) {
                case PushOrderToConnectors::TRIGGER_TYPE_AUTO:
                    $orderReferenceNeedsSave = true;
                    $this->orderReference->auto_triggered_at = date('Y-m-d H:i:s');
                    break;

                case PushOrderToConnectors::TRIGGER_TYPE_SCHEDULED:
                    $orderReferenceNeedsSave = true;
                    $this->orderReference->auto_scheduled_at = date('Y-m-d H:i:s');
                    break;

                case PushOrderToConnectors::TRIGGER_TYPE_MANUAL:
                    $orderReferenceNeedsSave = true;
                    $this->orderReference->manually_triggered_at = date('Y-m-d H:i:s');
                    break;
            }

            if($orderReferenceNeedsSave) {
                $this->orderReference->save();
            }
        }

        // Init connector service
        $settingConnector->setDeliveryMethodByOrderType($order->type);
        $hendrickxKassaConnector = new HendrickxKassaConnector($settingConnector);

        // Get free table if we do not have a remote ID
        if(empty($this->orderReference->remote_id)) {
            $tableNumberData = $hendrickxKassaConnector->getFreeTableNumber();
            Log::debug('HendrickxKassas $tableNumberData', ['response' => $tableNumberData]);

            if(empty($tableNumberData->TableNumber) || $tableNumberData->TableNumber === 'false') {
                Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int) $this->orderId) . '): Could not retrieve a free table number!' . "\n" . var_export($tableNumberData, true));

                $this->tryToRecover();
                return;
            }

            $this->orderReference->remote_id = $tableNumberData->TableNumber;
            $this->orderReference->save();
        }

        // Create order if we didn't already do this..
        if(empty($this->orderReference->order_synced_at)) {
            $orderRequestData = $this->mapOrderData($workspace, $settingConnector, $order, $this->orderReference, $productRepository, $optionItemRepository);
            Log::debug('HendrickxKassas $orderRequestData', ['request' => $orderRequestData]);

            $orderData = $hendrickxKassaConnector->createOrder($orderRequestData);
            Log::debug('HendrickxKassas $orderData', ['response' => $orderData]);

            if(empty($orderData->IsSuccessStatusCode) || $orderData->IsSuccessStatusCode === 'false') {
                Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): Failed creating order!' . "\n" . var_export($orderData, true));

                $this->tryToRecover();
                return;
            }

            $this->orderReference->order_synced_at = date('Y-m-d H:i:s');
            $this->orderReference->save();
        }

        // Do pay bill if this wasn't processed and we didn't select payment method "cash"
        if(
            empty($this->orderReference->payment_synced_at)
            && (
                // GROUP ORDER WE SHOULD SYNC PAYMENT IF WE HAVE ANY..
                (!empty($order->group_id) && !empty($order->total_paid))

                // NORMAL ORDER ONLY IF ITS PAID
                || !in_array($order->payment_method, [
                    \App\Models\SettingPayment::TYPE_CASH,
                    \App\Models\SettingPayment::TYPE_FACTUUR,
                ])
            )
        ) {
            $billRequestData = $this->mapBillData($workspace, $settingConnector, $order, $this->orderReference);
            Log::debug('HendrickxKassas $billRequestData', ['request' => $billRequestData]);

            // PARTIALLY PAY
            if(!empty($order->total_paid) && $order->total_paid < $order->total_price) {
                $billPaymentType = 'PostDeposit';
                $billData = $hendrickxKassaConnector->postDeposit($billRequestData);
            }
            // PAY BILL
            else {
                $billPaymentType = 'PayBill';
                $billData = $hendrickxKassaConnector->payBill($billRequestData);
            }

            Log::debug('HendrickxKassas $billData', ['response' => $billData]);

            if(empty($billData->IsSuccessStatusCode) || $billData->IsSuccessStatusCode === 'false') {
                Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): Failed '.$billPaymentType.'!' . "\n" . var_export($billData, true));

                $this->tryToRecover();
                return;
            }

            $this->orderReference->payment_synced_at = date('Y-m-d H:i:s');
            $this->orderReference->save();
        }

        // DONE
        $this->orderReference->completely_synced_at = date('Y-m-d H:i:s');
        $this->orderReference->save();
    }

    /**
     * @param Order $order
     * @param OrderReference $orderReference
     * @return array|void
     */
    protected function mapOrderData(Workspace $workspace, SettingConnector $settingConnector, Order $order, OrderReference $orderReference, ProductRepository $productRepository, OptionItemRepository $optionItemRepository) {
        $currentLang = app()->getLocale();
        $order = OrderHelper::sortOptionItems($order);
        $user = $order->user;
        $lang = $workspace->language;
        $codeId = "#" . ($order->group_id ? "G" . $order->parent_code : $order->code);

        $dateTimeLocal = Helper::convertDateTimeToTimezone($order->date . " " . $order->time, $order->timezone);
        $dateTimeLocalParse = Carbon::parse($dateTimeLocal);
        $dateLocal = $dateTimeLocalParse->format("d/m/y");
        $timeLocal = $dateTimeLocalParse->format("H:i");

        $dateTimeLocalGereed = Helper::convertDateTimeToTimezone($order->gereed, $order->timezone);
        $dateTimeLocalGereedParse = Carbon::parse($dateTimeLocalGereed);
        $gereedDateLocal = $dateTimeLocalGereedParse->format("d/m/y");
        $gereedTimeLocal = $dateTimeLocalGereedParse->format("H:i");

        // GROUP: We need to grab all ordered products
        if(!empty($order->group_id)) {
            $groupOrders = $order->groupOrders;
            $databaseOrderItems = $this->combineGroupOrderItems($groupOrders);
        }
        // NORMAL ORDER: grab products of current order
        else {
            $databaseOrderItems = $order->orderItems;
        }

        $productIds = $databaseOrderItems->pluck('product_id');
        $productReferences = null;
        if(!empty($productIds)) {
            $productReferences = $productRepository->getProductReferencesByWorkspaceAndProviderAndLocalIds($workspace->id, $settingConnector->provider, $productIds);
            $productReferences = $productReferences->keyBy('local_id');
        }

        // Grab item option ids
        $optionItemIds = [];
        foreach($databaseOrderItems as $orderItem) {
            foreach($orderItem->optionItems as $optionItem) {
                $optionItemIds[] = $optionItem->optie_item_id;
            }
        }

        $optionItemReferences = null;
        if(!empty($optionItemIds)) {
            $optionItemReferences = $optionItemRepository->getOptionItemReferencesByWorkspaceAndProviderAndLocalIds($workspace->id, $settingConnector->provider, $optionItemIds);
            $optionItemReferences = $optionItemReferences->keyBy('local_id');
        }

        $orderItems = [];
        foreach($databaseOrderItems as $orderItem) {
            $productMetas = json_decode($orderItem->metas);
            $product = $productMetas->product;

            /** @var ProductReference $productReference */
            $productReference = !empty($productReferences)
                ? $productReferences->get($orderItem->product_id)
                : null;

            if(empty($productReference)) {
                $error = 'PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): Cant find product ('.$product->id.') Remote ID!';
                $exception = new \Exception($error);
                $this->fail($exception);
                throw $exception;
            }

            $nameProduct = $product->name;
            $productOptions = $orderItem->optionItems;
            $groupedOptionItems = $productOptions->groupBy('optie_id');

            $variants = [];
            $missingOptionNames = '';
            $missingOptionPrices = [];

            foreach ($groupedOptionItems as $optionId => $optionItems) {
                $collectOptionItems = collect();
                $optionItemMetasFirstOption = null;

                foreach ($optionItems as $optionItem) {
                    /** @var ProductReference $productReference */
                    $optionItemReference = !empty($optionItemReferences)
                        ? $optionItemReferences->get($optionItem->optie_item_id)
                        : null;

                    $metas = json_decode($optionItem->metas);
                    $optionItemMetasFirstOption = !empty($metas->option[0]) ? $metas->option[0] : null;

                    $structuredOptionItem = new \stdClass();
                    $structuredOptionItem->master = $metas->option_item->master;
                    $structuredOptionItem->price = $metas->option_item->price;
                    $structuredOptionItem->name = $metas->option_item->name;
                    $structuredOptionItem->reference = !empty($optionItemReference) ? $optionItemReference->remote_id : null;
                    $collectOptionItems->push($structuredOptionItem);
                }

                $isMaster = $collectOptionItems->where('master', true)->first();

                // MASTER OPTION
                if($isMaster) {
                    $optionItemName = '';
                    if ($optionItemMetasFirstOption && $optionItemMetasFirstOption->is_ingredient_deletion) {
                        $optionItemName .= trans('cart.txt_zonder');
                    }

                    $optionItemName .= $isMaster->name;

                    if(!empty($isMaster->reference)) {
                        $variants[] = [
                            'ArticleId' => (int)$isMaster->reference,
                            'Text' => $optionItemName,
                            'Quantity' => 1,
                            'UnitPrice' => $isMaster->price,
                            'InfoText' => '',
                        ];
                    }
                    else {
                        if(!empty($missingOptionNames)) {
                            $missingOptionNames .= ', ';
                        }

                        $missingOptionNames .= $optionItemName;
                        $missingOptionPrices[] = $isMaster->price;
                    }
                }
                // OPTION LIST
                else {
                    foreach($collectOptionItems as $collectOptionItem) {
                        $optionItemName = '';
                        if ($optionItemMetasFirstOption && $optionItemMetasFirstOption->is_ingredient_deletion) {
                            $optionItemName .= trans('cart.txt_zonder');
                        }

                        $optionItemName .= $collectOptionItem->name;

                        if(!empty($collectOptionItem->reference)) {
                            $variants[] = [
                                'ArticleId' => (int)$collectOptionItem->reference,
                                'Text' => $optionItemName,
                                'Quantity' => 1,
                                'UnitPrice' => $collectOptionItem->price,
                                'InfoText' => ''
                            ];
                        }
                        else {
                            if(!empty($missingOptionNames)) {
                                $missingOptionNames .= ', ';
                            }

                            $missingOptionNames .= $optionItemName;
                            $missingOptionPrices[] = $collectOptionItem->price;
                        }
                    }
                }
            }

            $infoText = '';
            $infoText .= !empty($missingOptionNames)
                ? $missingOptionNames
                : '';

            if(!empty($orderItem->info_text)) {
                if(!empty($infoText)) {
                    $infoText .= ' | ';
                }

                $infoText .= $orderItem->info_text;
            }

            $unitPrice = (float) $product->price;
            foreach($missingOptionPrices as $missingOptionPrice) {
                $unitPrice += $missingOptionPrice;
            }

            // Apply discount
            if(!empty($orderItem->coupon_discount)) {
                $couponDiscount =  $orderItem->coupon_discount;
                if($orderItem->total_number > 1) {
                    $couponDiscount = $couponDiscount / $orderItem->total_number;
                }

                $unitPrice -= round($couponDiscount, 2);
            }

            // Apply redeem (loyality card)
            if(!empty($orderItem->redeem_discount)) {
                $redeemDiscount =  $orderItem->redeem_discount;
                if($orderItem->total_number > 1) {
                    $redeemDiscount = $redeemDiscount / $orderItem->total_number;
                }

                $unitPrice -= round($redeemDiscount, 2);
            }

            $orderItems[] = [
                'ArticleId' => (int) $productReference->remote_id,
                'Text' => $nameProduct,
                'InfoText' => $infoText,
                'UnitPrice' => (float) round($unitPrice, 2),
                'Quantity' => $orderItem->total_number,
                'Variants' => $variants
            ];
        }

        // Add product for takeout, delivery or in house
        // We will only include a product if we defined a product.
        /** @var SettingOpenHour|null $settingOpenHour */
        $settingOpenHour = null;
        switch($order->type) {
            case Order::TYPE_DELIVERY:
                $settingOpenHour = $workspace->settingOpenHours
                    ->where('type', \App\Models\SettingOpenHour::TYPE_DELIVERY)
                    ->where('active', 1)
                    ->first();
                break;

            case Order::TYPE_TAKEOUT:
                $settingOpenHour = $workspace->settingOpenHours
                    ->where('type', \App\Models\SettingOpenHour::TYPE_TAKEOUT)
                    ->where('active', 1)
                    ->first();
                break;

            case Order::TYPE_IN_HOUSE:
                $settingOpenHour = $workspace->settingOpenHours
                    ->where('type', \App\Models\SettingOpenHour::TYPE_IN_HOUSE)
                    ->where('active', 1)
                    ->first();

                break;
        }

        /** @var SettingOpenHourReference|null $settingOpenHourReference */
        $settingOpenHourReference = null;
        if(!empty($settingOpenHour)) {
            $settingOpenHourReference = $settingOpenHour->openHourReferences()
                ->where('provider', $settingConnector->provider)
                ->first();
        }

        // Add article for takeout, delivery or in house
        if(
            !empty($settingOpenHourReference)
            && !empty($settingOpenHourReference->remote_id)
        ) {
            $unitPrice = 0;
            if($settingOpenHour->type == SettingOpenHour::TYPE_DELIVERY) {
                $unitPrice = $order->ship_price;
            }

            $orderItems[] = [
                'ArticleId' => (int) $settingOpenHourReference->remote_id,
                'Text' => $settingOpenHour->getTypes($settingOpenHour->type),
                'InfoText' => '',
                'UnitPrice' => (float) round($unitPrice),
                'Quantity' => 1,
                'Variants' => []
            ];
        }

        // Show order name
        if(!empty($order->group_id)) {
            $orderFullName = !empty($order->group->name)
                ? $order->group->name . ' '
                : '';
        }
        else {
            $orderFullName = !empty($user->name)
                ? $user->name . ' '
                : '';
        }

        $tableText = $codeId . ': ' . $orderFullName . $gereedDateLocal . ' | ITR';

        // Add phone number
        if(!empty($order->group_id)) {
            $tableText .= !empty($order->group) ? " | " . $order->group->contact_gsm : '';
        }
        else {
            $tableText .= !empty($order->user) ? " | " . $order->user->gsm : '';
        }

        // Show delivery info
        if($order->type == Order::TYPE_DELIVERY) {
            if(!empty($order->group_id)) {
                $tableText .= " | " . trans('cart.success_levering_confirm') . ': ' . !empty($order->group) ? $order->group->address_display : '';
            }
            else {
                $tableText .= " | " . trans('cart.success_levering_confirm') . ': ' . $order->address;
            }
        }

        // Add note
        if(empty($order->group_id) && !empty($order->note)) {
            $tableText .= " | " . $order->note;
        }

        $data = [
            'TableNumber' => (int) $orderReference->remote_id,
            'OrderInfoText' => '',
            'TableText' => $tableText,
            'PickupTime' => $gereedTimeLocal,
            'Items' => $orderItems
        ];

        return $data;
    }

    protected function combineGroupOrderItems($groupOrders) {
        $databaseOrderItems = new Collection();

        if($groupOrders->isEmpty()) {
           return $databaseOrderItems;
        }

        // Create collections
        $databaseOrderItemsWithoutNote = new Collection();
        $databaseOrderItemsWithNote = new Collection();

        foreach($groupOrders as $groupOrder) {
            $groupOrderItems = $groupOrder->orderItems;

            if(!$groupOrderItems->isEmpty()) {
                // WITHOUT NOTE
                if(empty($groupOrder->note)) {
                    $databaseOrderItemsWithoutNote = $databaseOrderItemsWithoutNote->merge($groupOrderItems);
                }
                // WITH NOTE
                else {
                    $groupOrderUser = $groupOrder->user;
                    $groupOrderUserFullName = !empty($groupOrderUser->name)
                        ? $groupOrderUser->name . ' '
                        : '';

                    // Process NOTE
                    $groupOrderNote = $groupOrder->note;
                    $groupOrderItems = $groupOrderItems->map(function ($orderItem) use ($groupOrderUserFullName, $groupOrderNote) {
                        $orderItem->info_text = $groupOrderUserFullName . ': ' . $groupOrderNote;
                        return $orderItem;
                    });

                    $databaseOrderItemsWithNote = $databaseOrderItemsWithNote->merge($groupOrderItems);
                }
            }
        }

        // Merge orders..
        $databaseOrderItems = $databaseOrderItems->merge($databaseOrderItemsWithoutNote);
        $databaseOrderItems = $databaseOrderItems->merge($databaseOrderItemsWithNote);

        return $databaseOrderItems;
    }

    /**
     * @param Order $order
     * @param OrderReference $orderReference
     * @return array
     */
    protected function mapBillData(Workspace $workspace, SettingConnector $settingConnector, Order $order, OrderReference $orderReference) {
        $codeId = "#" . ($order->group_id ? "G" . $order->parent_code : $order->code);
        $dateTimeLocal = Helper::convertDateTimeToTimezone($order->date . " " . $order->time, $order->timezone);
        $dateTimeLocalParse = Carbon::parse($dateTimeLocal);
        $dateLocal = $dateTimeLocalParse->format("d/m/y");

        $user = $order->user;

        /** @var SettingPaymentReference|null $settingPaymentReference */
        $settingPaymentReference = null;

        // GROUP: We need to grab all ordered products
        if(!empty($order->group_id)) {
            $groupOrders = $order->groupOrders;

            $usedPaymentMethods = [];

            // Find order that used online payment to get the correct settings
            if(!$groupOrders->isEmpty()) {
                foreach($groupOrders as $groupOrder) {
                    $usedPaymentMethods[] = $groupOrder->payment_method;

                    if(
                        empty($settingPayment)
                        && $groupOrder->payment_method == \App\Models\SettingPayment::TYPE_MOLLIE
                    ) {
                        /** @var SettingPayment|null $settingPayment */
                        $settingPayment = $groupOrder->settingPayment()->first();
                    }
                }
            }

            // Add payment methods for group orders
            $paymentTypeText = '';
            foreach($usedPaymentMethods as $usedPaymentMethod) {
                if(isset(\App\Models\SettingPayment::getTypes()[$usedPaymentMethod])) {
                    if(!empty($paymentTypeText)) {
                        $paymentTypeText .= ', ';
                    }

                    $paymentTypeText .= \App\Models\SettingPayment::getTypes()[$usedPaymentMethod];
                }
            }

            // order name
            $orderFullName = !empty($order->group->name)
                ? $order->group->name . ' '
                : '';
        }
        // NORMAL ORDER: grab products of current order
        else {
            /** @var SettingPayment|null $settingPayment */
            $settingPayment = $order->settingPayment()->first();

            $paymentTypeText = !empty(\App\Models\SettingPayment::getTypes()[$order->payment_method])
                ? \App\Models\SettingPayment::getTypes()[$order->payment_method]
                : '';
            $orderFullName = !empty($user->name)
                ? $user->name . ' '
                : '';
        }

        if (!empty($settingPayment)) {
            $settingPaymentReference = $settingPayment->paymentReferences()
                ->where('provider', $settingConnector->provider)
                ->first();
        }

        if(empty($settingPaymentReference)) {
            Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): EMPTY settingPaymentReference - pushing payment will probably fail..');
        }

        $billInfoText = $codeId . ': ' . $orderFullName . $dateLocal . ' | ' . $paymentTypeText;

        // Add phone number
        if(!empty($order->group_id)) {
            $billInfoText .= !empty($order->user) ? "\n" . $order->user->gsm : '';
        }
        else {
            $billInfoText .= !empty($order->group) ? "\n" . $order->group->contact_gsm : '';
        }

        // Show delivery address
        if($order->type == Order::TYPE_DELIVERY) {
            $billInfoText .= "\n" . trans('cart.success_levering_confirm') . ': ' . $order->address;
        }

        $data = [
            'TableNumber' => (int) $orderReference->remote_id,
            'BillInfoText' => $billInfoText,
            'Payments' => [
                [
                    'Number' => !empty($settingPaymentReference)
                        ? (int) $settingPaymentReference->remote_id
                        : 0,
                    'Name' => !empty($settingPaymentReference)
                        ? $settingPaymentReference->remote_name
                        : 'Unkown',
                    'Amount' => (float) $order->total_paid,
                ]
            ]
        ];

        return $data;
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function logFailed(\Exception $exception) {
        Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): FATAL ERROR', (array) $exception);
    }

    /**
     * Replan task if possible
     * @param \Exception|null $exception
     * @return void
     */
    public function tryToRecover($exception = null) {
        // Log exception
        if($exception !== null) {
            $this->logFailed($exception);
        }

        // Send notify message (if first attempt fails)
        $this->sendNoticeMail();

        // Release job back in queue for re-processing
        $this->release($this->getReleaseDelay());
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(
        \Exception $exception
    ) {
        Log::error('PushOrderToConnectors\HendrickxKassas: OrderID (' . ((int)$this->orderId) . '): FATAL ERROR - WE STOP TRYING!', [
            'connectorId' => $this->connectorId,
            'orderId' => $this->orderId
        ]);

        /** @var Order|null|false $order */
        $order = Order::find((int) $this->orderId);

        $workspace = null;
        if(!empty($order)) {
            /** @var Workspace|null|false $workspace */
            $workspace = $order->workspace;
        }

        // Flag as failed
        if(!empty($this->orderReference)) {
            $this->orderReference->failed_at = date('Y-m-d H:i:s');
            $this->orderReference->save();
        }

        $this->sendFailedMail($workspace, $order, $this->attempts(), true);
    }

    protected function sendNoticeMail() {
        $attempts = $this->attempts();

        if($attempts == 1) {
            /** @var Order|null|false $order */
            $order = Order::find((int) $this->orderId);

            $workspace = null;
            if(!empty($order)) {
                /** @var Workspace|null|false $workspace */
                $workspace = $order->workspace;
            }

            $this->sendFailedMail($workspace, $order, $attempts);
        }
    }

    /**
     * @param $workspace
     * @param $order
     * @param integer $attempt Number of attempts
     * @param bool $isFatalError This means we stop trying because we already tried many times
     * @return void
     * @throws \Throwable
     */
    protected function sendFailedMail($workspace, $order, $attempt, $isFatalError = false) {
        // Send e-mail
        $template = 'emails.hendrickx_kassas_failed';
        $data = [
            'workspace' => $workspace,
            'order' => $order,
            'orderId' => $this->orderId,
            'connectorId' => $this->connectorId,
            'attempts' => $attempt,
            'maxAttempts' => $this->tries
        ];
        \App::setLocale($workspace->getLocale());
        $rawContent = Helper::stripHTMLCSS(view($template, $data)->render());
        $to = in_array(config('app.env'), ['stage', 'prod']) ? 'kurt@opwaerts.be' : env('DEVELOPER_EMAIL', 'kurt@opwaerts.be');
        Email::create([
            'to' => $workspace && $workspace->email ? $workspace->email : env('DEVELOPER_EMAIL', 'kurt@opwaerts.be'),
            'subject' => $isFatalError ? trans('mail.hendrickx_kassas_failed.subject_fatal') : trans('mail.hendrickx_kassas_failed.subject_notice'),
            'content' => $rawContent,
            'locale' => $workspace->getLocale(),
            'location' => json_encode([
                'id' => HendrickxKassaConnector::class,
            ])
        ]);
        \Mail::send([
            'html' => $template,
            'raw' => $rawContent,
        ], $data, function ($m) use ($workspace, $isFatalError, $to) {
            /** @var \Illuminate\Mail\Message $m */

            $fromEmail = config('mail.from.address');
            $fromName = config('mail.from.name');

            $m->from($fromEmail, $fromName);

            if(!empty($workspace)) {
                $m->to($workspace->email, $workspace->manager_name);
            }
            else {
                $m->to($to, 'Kurt Aerts');
            }

            if(in_array(config('app.env'), ['stage', 'prod'])) {
                // When empty we already send it this mailaddress
                if(!empty($workspace)) {
                    $m->addBcc('kurt@opwaerts.be', 'Kurt Aerts'); // To make sure it works
                }

                $m->addBcc('sebastian_mathieu@hotmail.com', 'Sebastian Mathieu'); // To make sure it works
            }

            if($isFatalError) {
                $m->subject(trans('mail.hendrickx_kassas_failed.subject_fatal'));
            }
            else {
                $m->subject(trans('mail.hendrickx_kassas_failed.subject_notice'));
            }
        });
    }

    /**
     * Returns an incremental delay time
     *
     * @return int
     */
    protected function getReleaseDelay() {
        // Attempt: 9, 10 (elapsed time: 20 minutes, total: 23 minutes and 30 seconds)
        if($this->attempts() > 8) {
            return 600; // 60 * 10 = 600 (10 minutes)
        }

        // Attempt: 7, 8 (elapsed time: 10 minutes, total: 13 minutes and 30 seconds)
        if($this->attempts() > 6) {
            return 300; // 60 * 5 = 300 (5 minutes)
        }

        // Attempt: 4, 5, 6 (elapsed time: 3 minutes, total: 3 minutes and 30 seconds)
        if($this->attempts() > 3) {
            return 60; // 60 (1 minutes)
        }

        // Attempt: 1, 2, 3 (elapsed time: 30 seconds)
        return 10; // 10 seconds
    }
}