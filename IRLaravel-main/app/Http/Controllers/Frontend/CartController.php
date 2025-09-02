<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Group;
use App\Models\Order;
use App\Repositories\CartItemRepository;
use App\Repositories\CartOptionItemRepository;
use App\Repositories\CartRepository;
use App\Repositories\CouponRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingTimeslotDetailRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WorkspaceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash, Session;
use Log;

class CartController extends BaseController
{
    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * @var CartItemRepository
     */
    public $cartItemRepository;

    /**
     * @var CartOptionItemRepository
     */
    public $cartOptionItemRepository;

    /**
     * @var OrderRepository
     */
    public $orderRepository;

    /**
     * @var SettingTimeslotDetailRepository
     */
    public $settingTimeslotDetailRepository;

    /**
     * @var productRepository
     */
    public $productRepository;

    /**
     * @var WorkspaceRepository
     */
    public $workspaceRepository;

    /**
     * @var CouponRepository
     */
    public $couponRepository;

    /**
     * CartController constructor.
     *
     * @param CartRepository                  $cartRepository
     * @param CartItemRepository              $cartItemRepository
     * @param CartOptionItemRepository        $cartOptionItemRepository
     * @param OrderRepository                 $orderRepository
     * @param SettingTimeslotDetailRepository $settingTimeslotDetailRepository
     * @param ProductRepository               $productRepository
     * @param WorkspaceRepository             $workspaceRepository
     * @param CouponRepository                $couponRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        CartOptionItemRepository $cartOptionItemRepository,
        OrderRepository $orderRepository,
        SettingTimeslotDetailRepository $settingTimeslotDetailRepository,
        ProductRepository $productRepository,
        WorkspaceRepository $workspaceRepository,
        CouponRepository $couponRepository
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartOptionItemRepository = $cartOptionItemRepository;
        $this->orderRepository = $orderRepository;
        $this->settingTimeslotDetailRepository = $settingTimeslotDetailRepository;
        $this->productRepository = $productRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (count(session()->get('productNotAvailable')) > 0 && $request->is_trigger_del != 1) {
            return redirect()->back()->withInput();
        }

        // Delete item without login
        if (session()->has('cart_without_login_'.$this->workspaceSlug)) {
            $cart        = session()->get('cart_without_login_'.$this->workspaceSlug);
            $idsCartItem = $cart->cartItems->pluck('id')->toArray();
            $idsItemDel  = array_diff($idsCartItem, array_keys($request->cartItem ?: []));

            foreach ($cart->cartItems as $k => $item) {
                if (in_array($item->id, $idsItemDel)) {
                    unset($cart->cartItems[$k]);
                }
            }

            // Add coupon if have
            $cart->coupon_id = NULL;
            $cart->coupon    = NULL;
            if ($request->coupon_id) {
                $cart->coupon_id = $request->coupon_id;
                $cart->coupon = $this->couponRepository->findWhere(['id' => $request->coupon_id])->first();
            }

            session(['cart_without_login_'.$this->workspaceSlug => $cart]);

            return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=1");
        }

        try {
            \DB::beginTransaction();

            $step          = (int) $request->step;
            $nowByTimezone = Carbon::now($request->timezone);
            $stepRedirect  = 1;

            // Step 1
            if ($step === 1) {
                // Comment to fix https://vitex1.atlassian.net/browse/ITR-979
//                if ($request->coupon_code && !$request->redeem_history_id) {
//                    $this->productRepository->validateProductCoupon(
//                        $request->listProductInCart,
//                        $request->coupon_code,
//                        $request->user_id,
//                        $request->workspace_id
//                    );
//                }

                $cartItems       = $request->cartItem ?: array();
                $cartOptionItems = $request->cartOptionItem ?: array();
                $cart            = $request->only([
                    'workspace_id',
                    'user_id',
                    'group_id',
                    'coupon_id',
                    'redeem_history_id',
//                    'redeem_discount',
                    'type',
                    'address_type',
                    'address',
                    'note',
                    'timezone',
                ]);

                if (!$cart['redeem_history_id']) {
                    $cart['redeem_discount'] = NULL;
                }

                if (isset($request->address_group)) {
                    $cart['address'] = $request->address_group;
                }

                $this->cartRepository->updateAndWhere(['id' => $request->cart_id], $cart);

                $this->cartItemRepository->deleteWhere([
                    'workspace_id' => $request->workspace_id,
                    'cart_id'      => $request->cart_id
                ]);

                $mapIds = array();
                foreach ($cartItems as $oldId => $cartItem) {
                    $cartItem['workspace_id'] = $request->workspace_id;
                    $cartItem['cart_id']      = $request->cart_id;
                    $newObj                   = $this->cartItemRepository->create($cartItem);
                    $mapIds[$oldId]           = $newObj->id;
                }

                foreach ($cartOptionItems as $cartOptItem) {
                    $cartOptItem = \GuzzleHttp\json_decode($cartOptItem, true);
                    foreach ($cartOptItem as $k => $item) {
                        $item['workspace_id'] = $request->workspace_id;
                        $item['cart_item_id'] = $mapIds[$item['cart_item_id']];
                        $cartOptItem[$k]      = $item;
                    }
                    $this->cartOptionItemRepository->saveMany($cartOptItem);
                }

                $stepRedirect = 2;

                if (count($cartItems) === 0 || $request->is_trigger_del == 1 || $request->submitByCoupon) {
                    $stepRedirect = 1;
                }
            }

            // Step 2
            if ($step === 2) {
                if (session()->has('payment_time_limit')) {
                    Session::forget('payment_time_limit');
                }

                $settingTimeslot = $request->settingTimeslot;
                $idTimeSlot = $request->idTimeSlot;

                // For group
                if ($request->groupId) {
                    $settingTimeslot = $request->groupReceiveTime;

                } elseif (!$settingTimeslot) {

                    // Check time empty
                    Flash::error(trans('cart.check_time_null'));
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                }

                // Check date empty
                if (!$request->settingDateslot) {
                    Flash::error(trans('cart.check_date_null'));
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                }

                $dateParse = Carbon::parse(str_replace('/', '-', $request->settingDateslot));
                $timeParse = Carbon::parse($settingTimeslot);

                $date     = $dateParse->format("Y-m-d");
                $hour     = $timeParse->format("H:i");
                $timeFull = $timeParse->format('H:i:s');
                $timeNow  = $nowByTimezone->format("Y-m-d H:i:s");
                $dateNow  = $nowByTimezone->format("Y-m-d");

                $dateOfWeek = $dateParse->dayOfWeek;
                $weekMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
                $weekday = $weekMap[$dateOfWeek];

                $cart = $this->cartRepository->with([
                    'cartItems.product.openTimeslots',
                    'workspace.settingTimeslots',
                ])
                ->findWhere(['id' => $request->cart_id])
                ->first();

                // Check item has available - Tijdsloten dynamisch beheren
                if (isset($cart->cartItems) && $cart->cartItems->count() > 0) {
                    $productIdsFail = array();
                    $isWeekdayError = false;

                    /** @var \App\Models\OrderItem $cartItem */
                    foreach ($cart->cartItems as $cartItem) {
                        /** @var \App\Models\Product $product */
                        $product       = $cartItem->product;
                        $openTimeslots = $product->openTimeslots;
                        $isAvailable   = false;

                        /** @var \App\Models\OpenTimeslot $time */
                        foreach ($openTimeslots as $time) {
                            if ($time->day_number === $weekday && !$time->status && $product->time_no_limit) {
                                $isWeekdayError = true;
                            }

                            if ($time->day_number === $weekday && $time->status) {
                                if ($request->groupId) {
                                    // Order type is Group
                                    if (($dateParse->toDateString() == $nowByTimezone->toDateString()
                                            && $timeFull >= $time->start_time && $timeFull <= $time->end_time)
                                        || ($dateParse->toDateString() > $nowByTimezone->toDateString())
                                    ) {
                                        $isAvailable = true;
                                        break;
                                    }
                                } else {
                                    // Order type is Individual
                                    if (($request->countMax < $request->max
                                            && $request->countMoney < $request->money)
                                        && ($timeFull >= $time->start_time && $timeFull <= $time->end_time)
                                    ) {
                                        $isAvailable = true;
                                        break;
                                    }
                                }
                            }
                        }

                        // Always when setting is Always available
                        if (!$product->time_no_limit) {
                            $isAvailable = true;
                        }

                        if (!$isAvailable) {
                            $productIdsFail[] = $product->id;
                            $stepRedirect = 1;
                        }
                    }

                    session()->put('idsProductFail', array_unique($productIdsFail));
                    session()->put('isWeekdayError', $isWeekdayError);

                    if (count($productIdsFail) > 0) {
                        return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=" . $stepRedirect);
                    }
                }

                // Check date empty
                if ($date < $dateNow) {
                    Flash::error(trans('cart.check_in_pass'));
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                }

                // Check holiday
                $holidays = collect(\GuzzleHttp\json_decode($request->holidays));
                if ($holidays->where("start_time", "<=", $date . " 00:00:00")->where("end_time", ">=", $date . " 00:00:00")->count() > 0) {
                    Flash::error(trans('cart.msg_holiday'));
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                }

                // For takout, levering
                if (!$request->groupId) {

                    // Check opening hours
                    $openHours = collect(\GuzzleHttp\json_decode($request->openHours));
                    $dayInWeekActive = $openHours->pluck('day_number')->toArray();
                    if (!in_array($dateOfWeek, $dayInWeekActive)) {
                        $stepRedirect = 2;
                        Flash::error(trans('cart.msg_open_hour'));
                    } else {
                        $isOpenHour = $openHours->where("day_number", $dateOfWeek)
                            ->where("start_time", "<=", $timeFull)
                            ->where("end_time", ">=", $timeFull)
                            ->count();

                        if (!$isOpenHour) {
                            Flash::error(trans('cart.msg_open_hour'));
                            return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                        }

                        if (!$timeFull) {
                            Flash::error(trans('cart.check_date_null'));
                            return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                        }
                    }

                    // Check setting: Voorkeuren - "Dagen vooruit bestellen:"
                    $dagenVooruitBestellen = TRUE;
                    $maxDate = $nowByTimezone->addDays((int) $request->offsetDayOrder)->format("Y-m-d");
                    if ($date < $dateNow || $date > $maxDate) {
                        $dagenVooruitBestellen = FALSE;
                    }

                    // Check Tijdslots - Bestellen tot maximaal
                    $isTijdslots = TRUE;
                    if ($cart->workspace && $cart->workspace->settingTimeslots) {
                        $settingTimeslots  = $cart->workspace->settingTimeslots;
                        $timeSlot          = $settingTimeslots->where('type', $cart->type)->first();
                        $listDayAvalidable = explode(',', $timeSlot->max_days);
                        $dateBefore        = $dateParse->addDays(-$timeSlot->max_before)->format("Y-m-d");

                        if ($timeSlot->max_mode && in_array($dateOfWeek, $listDayAvalidable)) {
                            $isTijdslots = $timeNow <= $dateBefore . " " . $timeSlot->max_time;
                        }
                    }

                    // Update id of time slot if true
                    if ($isTijdslots && $dagenVooruitBestellen) {
                        if (!isset($productIdsFail) || (isset($productIdsFail) && empty($productIdsFail))) {
                            $dateTimeUTC = Helper::convertDateTimeToUTC($date . " " . $hour, $request->timezone);

                            $this->cartRepository->updateAndWhere(['id' => $request->cart_id], [
                                'date'                       => $dateTimeUTC->format('Y-m-d'),
                                'time'                       => $dateTimeUTC->format('H:i:s'),
                                'date_time'                  => $dateTimeUTC->format('Y-m-d H:i:s'),
                                'setting_timeslot_detail_id' => $idTimeSlot,
                            ]);

                            $stepRedirect = 3;
                        }

                    } else {

                        if (!$isTijdslots) {
                            $stepRedirect = 2;
                            Flash::error(trans('cart.check_tijdslots'));
                        }
                        if (!$dagenVooruitBestellen) {
                            $stepRedirect = 2;
                            Flash::error(trans('cart.check_dagen_vooruit_bestellen'));
                        }
                    }

                } else {

                    // For group
                    $stepRedirect = 2;
                    $timeNow = $nowByTimezone->format("H:i");
                    $openTimeslots = \GuzzleHttp\json_decode($request->openTimeslotsGroup);

                    //Check valid time group before submit to step 3
                    if ($dateNow >= $date && $request->closeTimeGroup < $timeNow) {
                        Flash::error(trans('cart.check_valid_date_group'));
                        return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                    }

                    if ($date === $dateNow && $request->closeTimeGroup > $timeNow) {
                        $stepRedirect = 3;
                    }

                    foreach ($openTimeslots as $timeSlot) {
                        if ($timeSlot->status && $timeSlot->day_number === $weekday) {
                            $stepRedirect = 3;
                            break;
                        }
                    }

                    if ($stepRedirect === 3) {
                        $dateTimeUTC = Helper::convertDateTimeToUTC($date . " " . $timeFull, $request->timezone);

                        $this->cartRepository->updateAndWhere(['id' => $request->cart_id], [
                            'date'      => $dateTimeUTC->format('Y-m-d'),
                            'time'      => $dateTimeUTC->format('H:i:s'),
                            'date_time' => $dateTimeUTC->format('Y-m-d H:i:s'),
                        ]);
                    }

                    if ($stepRedirect === 2) {
                        Flash::error(trans('cart.check_date_group'));
                    }
                }

                if($stepRedirect == 3) {
                    session(['payment_time_limit' => now()->timestamp]);
                }
            }

            // Step 3
            if ($step === 3) {
                if (session()->has('payment_time_limit')) {
                    $paymentTimeLimit = \Carbon\Carbon::parse(date('Y-m-d H:i:s', session('payment_time_limit')));
                    session()->forget('payment_time_limit');
                    $now = now();
                    $duration = $now->diffInMinutes($paymentTimeLimit);

                    if($duration > 5) {
                        session(['is_desired_time_limit' => true]);
                        return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                    }
                } else {
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                }

                $payments = explode('-', $request->setting_payment_id);
                $paymentMethod = $payments[1] ?? NULL;

                if (is_null($paymentMethod)) {
                    return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=3");
                }

                $cart = $this->cartRepository->with([
                    'coupon',
                    'workspace',
                    'cartItems',
                    'cartItems.cartOptionItems',
                    'cartItems.cartOptionItems.optionItem'
                ])
                ->findWhere(['id' => $request->cart_id])
                ->first();

                // For takout, levering
                if ($request->group_id) {
                    $timeNow = $nowByTimezone->format("H:i");
                    $dateNow  = $nowByTimezone->format("Y-m-d");
                    //Check valid time group before submit to step 3
                    if ($dateNow >= $cart->date && $request->closeTimeGroup <= $timeNow) {
                        Flash::error(trans('cart.check_valid_date_group'));
                        return redirect($request->currentUrl . "?tab=" . $request->tab . "&step=2");
                    }
                }

                if (!$cart->workspace->is_online) {
                    return redirect()->back()->withErrors(['workspace_offline' => trans('messages.workspace_offline')]);
                }

                $cart->update(['setting_payment_id' => $payments[0] ?: NULL]);

                // Pay by Mollie
                // Case test account auth()->user()->isSuperAdmin()
                if ($paymentMethod == \App\Models\SettingPayment::TYPE_MOLLIE && $cart->total_price > 0 && !auth()->user()->isSuperAdmin()) {

                    \DB::commit();

                    if ($request->group_id) {
                        $group = Group::find($request->group_id);
                        if ($group && !$group->active) {
                            throw new \Exception(trans('group.inactive'), 500);
                        }
                    }

                    $oldUrl = $request->currentUrl . "?step=3";
                    $urlStep2 = $request->currentUrl . "?step=2";
                    if ($request->tab) {
                        $oldUrl = $request->currentUrl . "?tab=" . $request->tab . "&step=3";
                        $urlStep2 = $request->currentUrl . "?tab=" . $request->tab . "&step=2";
                    }

                    $mollieRedirectParams = [$request->cart_id, encrypt($oldUrl), encrypt($urlStep2)];

                    // Order from a restaurant website
                    if ($request->has('workspace_id')) {
                        $mollieRedirectParams['template_id'] = (int)$request->get('workspace_id');
                    }

                    return redirect(route("web.mollie.index", $mollieRedirectParams));

                } else { // Other pay

                    $condition = $this->workspaceRepository->getDeliveryConditions($cart->workspace_id, [
                        'lat'  => $cart->lat,
                        'lng'  => $cart->long,
                    ])->first();

                    $data = Helper::createDataOrder($cart, $paymentMethod, $condition);

                    // Make default is pending
                    $data['status'] = Order::PAYMENT_STATUS_PENDING;

                    // Order from a restaurant website
                    if ($request->has('workspace_id')) {
                        $data['template_id'] = (int)$request->get('workspace_id');
                    }

                    // Case test account auth()->user()->isSuperAdmin()
                    if (auth()->user()->isSuperAdmin()) {
                        $data['status'] = Order::PAYMENT_STATUS_PAID;
                        $data['is_test_account'] = Order::IS_TEST_ACCOUNT;
                    }

                    $order = $this->orderRepository->create($data);

                    // Case test account auth()->user()->isSuperAdmin()
                    if (auth()->user()->isSuperAdmin()) {
                        $order->total_paid = $order->total_price;
                        $order->payed_at = now();
                        $order->save();
                    }

                    $this->cartRepository->deleteWhere(['id' => $cart->id]);

                    \DB::commit();

                    return redirect(route('web.orders.show', $order->id));
                }
            }

            \DB::commit();

            $url = $request->currentUrl . "?step=" . $stepRedirect;

            if ($request->tab) {
                $url = $request->currentUrl . "?tab=" . $request->tab . "&step=" . $stepRedirect;
            }

            return redirect($url);

        } catch (\Exception $exc) {
            \DB::rollback();

            if ($exc->getCode() === 500) {
                return redirect()->back()->withErrors(['incorrect_time_slot' => $exc->getMessage()]);
            }

            Log::error($exc->getTraceAsString());

            return response()->json([
                'code'    => $exc->getCode(),
                'message' => $exc->getMessage()
            ]);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function orderAgain(Request $request, int $id) {
        $check = true;
        $checkValidOption = true;
        $order = $this->orderRepository->find($id);

        //Check order
        if (empty($order)) {
            return redirect(route('web.error'));
        }

        $orderItems = $order->orderItems;

        //Check order items
        if (empty($orderItems)) {
            return redirect(route('web.error'));
        }

        $orderItemIds =  array_unique($orderItems->pluck('product_id')->toArray());
        $getProductOrders = $this->productRepository->makeModel()
            ->whereIn('id', $orderItemIds)
            ->where('active', 1)
            ->get();

        //Check group
        if ($order->parent_id || $order->group_id) {
            $checkGroup = Group::where('id', $order->group_id)->count();
            if (!$checkGroup) {
                Flash::error(trans('frontend.message_for_group_not_exist'));

                return back();
            }
        }

        try {
            \DB::beginTransaction();

            //Remove old cart
            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

            //Store cart
            $cartArray = $this->cartRepository->convertOrderToArray($request, $order);
            $cart = $this->cartRepository->create($cartArray);
            $countInvalidOptionItem = 0;
            $orderOptiesId = [];
            foreach ($orderItems as $value) {
                $newOtp = $optionCheck = array();
                $getProductOrder = $this->productRepository->makeModel()
                    ->with('productOptions')
                    ->where('id', $value->product_id)
//                    ->where('active', 1)
                    ->first();

                //Process check min - max
                $orderOptiesId = $value->optionItems->pluck('optie_id')->toArray();
                $_productOptions = !empty($getProductOrder) && $getProductOrder->productOptions->count() > 0 ? $getProductOrder->productOptions()->where('is_checked', 1)->get() : null;
                if (!empty($getProductOrder) && !empty($_productOptions)) {
                    foreach ($_productOptions as $_item) {
                        $option = $_item->option;
                        if (!empty($option) && in_array($_item->opties_id, $orderOptiesId)) {
                            $optionCheck[$_item->opties_id] =  [
                                'min' => $option->min,
                                'max' => $option->max,
                                'count' => 0
                            ];
                        }
                    }
                }

                if (!empty($getProductOrder)
                    && !empty($getProductOrder->category)
                    && $getProductOrder->category()->count()
                ) {
                    $cartItemArray = $this->cartRepository->convertOrderItemsToArray($value, $cart);
                    //Store cart items
                    $newCartitemObj = $this->cartItemRepository->create($cartItemArray);

                    //Store cart items option
                    if (!empty($value->optionItems)) {
                        //Valid option min - max
                        foreach ($value->optionItems as $item) {
                            if(!empty($newOtp[$item['optie_id']])) {
                                $newOtp[$item['optie_id']] = $newOtp[$item['optie_id']] + 1;
                            } else {
                                $newOtp[$item['optie_id']] = 1;
                            }
                            $optionCheck[$item['optie_id']]['count'] = $newOtp[$item['optie_id']];
                        }

                        //Valid option before book
                        if (!empty($optionCheck)) {
                            foreach ($optionCheck as $_value) {
                                if (isset($_value['min']) && isset($_value['max'])) {
                                    if (($_value['count'] < $_value['min']) || ($_value['count'] > $_value['max'])) {
                                        $checkValidOption = false;
                                    }
                                } else {
                                    $checkValidOption = false;
                                }
                            }
                        }

                        $optionItems = $this->cartRepository->convertOrderItemsOptionToArray($value->optionItems, $newCartitemObj, $countOptionNotAvailable);
                        $countInvalidOptionItem += $countOptionNotAvailable;
                        $this->cartOptionItemRepository->saveMany($optionItems);
                    }

                    $check = false;
                }
            }

            \DB::commit();

            if ($check || !$checkValidOption) {
                $countInvalidOptionItem++;
            }

            $condition = [];
            if (empty($cart->group_id) && isset($cart->address_type)) {
                $condition = $this->workspaceRepository->getDeliveryConditions($request->workspaceId, [
                    'lat'  => $cart->lat,
                    'lng'  => $cart->long,
                ])->first();

                if (!$condition) {
                    session()->flash('address_not_avaliable', 1);
                }
            }

            //Check products
            if ($getProductOrders->isEmpty()) {
                Flash::error(trans('frontend.message_for_product', ['count' => 1]));
            } elseif ($getProductOrders->count() != $orderItems->count()) {
                Flash::error(trans('frontend.message_for_product', ['count' => 1]));
            } elseif ($countInvalidOptionItem) {
                Flash::error(trans('frontend.message_for_option', ['count' => 1]));
            }

            $orderType = $order->type && !$order->group_id && $condition
                ? Cart::TAB_LEVERING
                : Cart::TAB_TAKEOUT;

            $url = !empty(session('urlType'))
                ? session('urlType')
                : route('web.user.index') . "?tab=" .$orderType . "&again=1&step=1";

            return redirect($url);

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getTraceAsString());

            return redirect(route('web.error'));
        }
    }

    public function checkGroupDate(Request $request)
    {
        $cartId = $request->cartId;
        $cart = $this->cartRepository->find($cartId);
        if ($cart && $cart->group) {
            return $this->sendResponse(['closeTimeGroup' => $cart->group->close_time], trans('strings.success'));
        }

        return $this->sendError(trans('strings.error'));
    }

    public function updateQuantity(Request $request)
    {
        if (session()->has('cart_without_login_'.$this->workspaceSlug)) {
            $cart = session()->get('cart_without_login_'.$this->workspaceSlug);
            foreach ($cart->cartItems as $cartItems) {
                if($cartItems->id == $request->cartItemId && $cartItems->total_number != $request->totalNumber) {
                    $cartItems->total_number = $request->totalNumber;
                }
            }
            session(['cart_without_login_'.$this->workspaceSlug => $cart]);
            return response()->json([],204);
        }elseif(!empty($request->cartItemId)) {
            $cartItem = CartItem::find($request->cartItemId);

            if(!empty($cartItem) && $cartItem->total_number != $request->totalNumber) {
                $cartItem->total_number = $request->totalNumber;
                $cartItem->save();
            }
            return response()->json([],204);
        }
        return response()->json([
            'code' => 500
        ],500);
    }
}
