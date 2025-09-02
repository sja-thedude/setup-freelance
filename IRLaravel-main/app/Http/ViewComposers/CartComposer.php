<?php

namespace App\Http\ViewComposers;

use App\Helpers\GroupHelper;
use App\Helpers\Helper;
use App\Helpers\TimeslotHelper;
use App\Models\Product;
use App\Repositories\CartRepository;
use App\Repositories\CouponRepository;
use App\Repositories\LoyaltyRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RewardRepository;
use App\Repositories\SettingDeliveryConditionsRepository;
use App\Repositories\SettingTimeslotDetailRepository;
use App\Repositories\WorkspaceRepository;
use Carbon\Carbon;
use Illuminate\View\View;

class CartComposer
{
    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * @var CouponRepository
     */
    public $couponRepository;

    /**
     * @var WorkspaceRepository
     */
    public $workspaceRepository;

    /**
     * @var SettingDeliveryConditionsRepository
     */
    public $settingDeliveryConditionsRepository;

    /**
     * @var LoyaltyRepository
     */
    public $loyaltyRepository;

    /**
     * @var RewardRepository
     */
    public $rewardRepository;

    /**
     * @var SettingTimeslotDetailRepository
     */
    public $settingTimeslotDetailRepository;

    /**
     * @var OrderRepository
     */
    public $orderRepository;

    /**
     * CartComposer constructor.
     *
     * @param CartRepository                      $cartRepository
     * @param CouponRepository                    $couponRepository
     * @param WorkspaceRepository                 $workspaceRepository
     * @param OrderRepository                     $orderRepository
     * @param SettingDeliveryConditionsRepository $settingDeliveryConditionsRepository
     * @param LoyaltyRepository                   $loyaltyRepository
     * @param RewardRepository                    $rewardRepository
     * @param SettingTimeslotDetailRepository     $settingTimeslotDetailRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        CouponRepository $couponRepository,
        WorkspaceRepository $workspaceRepository,
        OrderRepository $orderRepository,
        SettingDeliveryConditionsRepository $settingDeliveryConditionsRepository,
        LoyaltyRepository $loyaltyRepository,
        RewardRepository $rewardRepository,
        SettingTimeslotDetailRepository $settingTimeslotDetailRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->couponRepository = $couponRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->orderRepository = $orderRepository;
        $this->settingDeliveryConditionsRepository = $settingDeliveryConditionsRepository;
        $this->loyaltyRepository = $loyaltyRepository;
        $this->rewardRepository = $rewardRepository;
        $this->settingTimeslotDetailRepository = $settingTimeslotDetailRepository;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        try {
            session()->forget('product_not_avaliable');
            session()->forget('modal_fill_address');
            $workspaceSlug = Helper::getSubDomainOfRequest();

            $productSuggesstions = array();
            $workspaceId         = request()->get('workspaceId');
            $userId              = !auth()->guest() ? auth()->user()->id : NULL;
            $type                = \App\Models\Cart::TYPE_TAKEOUT;

            $isDeleveringAvailable = TRUE;
            $isDeleveringPriceMin  = TRUE;
            $isShowMessageFree     = FALSE;
            $condition             = NULL;
            $priceDiscount         = 0;
            $totalCouponDiscount   = 0;
            $discountProducts      = [];
            $timeSlot              = array();
            $listDayAvalidable     = array();
            $datesDisable          = array();
            $productIds            = array();
            $infoRedeem            = NULL;
            $redeem                = NULL;
            $totalRedeemDiscount   = NULL;
            $couponDiscount        = 0;
            $groupDiscount         = 0;
            $failedProducts        = [];
            $failedOpties        = [];
            $memoryCacheService = \App\Services\MemoryCacheService::getInstance();

            if($memoryCacheService->get('compose', $workspaceId .'-' . (string) $userId, null, true) === null) {
                $cart = $this->cartRepository->with([
                    'cartItems',
                    'group',
                    'coupon',
                    'workspace',
                    'workspace.workspaceExtras',
                    'workspace.settingDeliveryConditions',
                    'workspace.settingPreference',
                    'workspace.settingOpenHours',
                    'workspace.settingPayments',
                    'workspace.settingTimeslots',
                    'workspace.settingExceptHoursExtend',
                    'cartItems.product.category',
                    'cartItems.product.vat',
                    'cartItems.cartOptionItems',
                    'cartItems.cartOptionItems.option',
                    'cartItems.cartOptionItems.optionItem',
                    'cartItems.category.productSuggestions.product',
                ])
                ->findWhere([
                    'workspace_id' => $workspaceId,
                    'user_id'      => (string) $userId,
                ])
                ->first();
                $memoryCacheService->set('compose', $workspaceId .'-' . (string) $userId, $cart, true);
            } else {
                $cart = $memoryCacheService->get('compose', $workspaceId .'-' . (string) $userId, null);
            }

            // With not logged
            if (!$userId && session()->has('cart_without_login_'.$workspaceSlug)) {
                $cart = session()->get('cart_without_login_'.$workspaceSlug);
                $cartInDB = $this->cartRepository->findWhere([
                    'workspace_id' => $workspaceId,
                    'user_id'      => session()->get('user_id_not_login'),
                ])->first();

                if ($cartInDB) {
                    $cart->address_type = $cartInDB->address_type;
                    $cart->address      = $cartInDB->address;
                    $cart->lat          = $cartInDB->lat;
                    $cart->long         = $cartInDB->long;
                }
            }

            $offsetDayOrder = isset($cart->workspace->settingPreference)
                ? $cart->workspace->settingPreference->takeout_day_order
                : 0;
            $offsetTimeOrder = isset($cart->workspace->settingPreference)
                ? $cart->workspace->settingPreference->takeout_min_time
                : 0;

            if (request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING) {
                $type           = \App\Models\Cart::TYPE_LEVERING;
                $offsetDayOrder = $cart->workspace->settingPreference->delivery_day_order;
                $offsetTimeOrder = $cart->workspace->settingPreference->delivery_min_time;
            }else if(!request()->has('tab') && !empty($cart)) {
                $type = $cart->type;
            }

            // Neu la group thi type dua tren seting cua group
            if ($cart && $cart->group) {
                $type = $cart->group->type;
                if (GroupHelper::isApplyGroupDiscount($cart)) {
                    $groupDiscount = GroupHelper::calculateGroupDiscount($cart);
                }
            }

            // Update lai Type neu chuyen tab
            if ($cart && $userId) {
                $this->cartRepository->update(['type' => $type], $cart->id);
                $cart->type = $type;
            }

            if ($cart) {
                $failedOpties = Helper::getFailedOpties($cart);
            }

            $openHours = NULL;
            $dayInWeekActive = NULL;
            if (isset($cart->workspace->settingOpenHours)) {
                $settingOpenHours = $cart->workspace->settingOpenHours->where('type', $type)->first();
                if ($settingOpenHours && $settingOpenHours->openTimeSlots) {
                    $openHours       = $settingOpenHours->openTimeSlots->toArray();
                    $dayInWeekActive = $settingOpenHours->openTimeSlots
                        ->sortBy('day_number')
                        ->pluck('day_number')
                        ->toArray();

                    // Check time in day now past
                    $timeParse       = Carbon::now($cart->timezone);
                    $timeNow         = $timeParse->format("H:i");
                    $dayOfWeek       = $timeParse->dayOfWeek;
                    $dayOpenHour     = $settingOpenHours->openTimeSlots->where('day_number', $dayOfWeek);
                    $isDisableDayNow = TRUE;
                    foreach ($dayOpenHour as $hour) {
                        if ($hour->end_time > $timeNow) {
                            $isDisableDayNow = FALSE;
                            break;
                        }
                    }
                    if ($isDisableDayNow) {
                        $datesDisable[] = $timeParse->format("Y-m-d");
                    }
                }
            }

            $holidays = isset($cart->workspace->settingExceptHoursExtend)
                ? $cart->workspace->settingExceptHoursExtend->toArray()
                : NULL;

            request()->request->add([
                'workspace_id'   => $workspaceId,
                'active'         => TRUE,
                'is_expire_time' => TRUE,
                'hide_expire_time' => TRUE,
                'hide_max_time_all' => TRUE,
            ]);

            $coupons = $this->couponRepository->paginate(1000);

            // Product suggesstion
            $numberCategory = $cart && isset($cart->cartItems) && $cart->cartItems->count() > 0
                ? $cart->cartItems->groupBy('category_id')->count()
                : 0;
            if ($numberCategory === 1 && $cart->cartItems->first()) {
                $productIds = $cart->cartItems->first()->category->productSuggestions->pluck('product_id')->toArray();
                $categoryIds = $cart->cartItems->first()->category->categoriesRelation->pluck('category_id')->toArray();
                $productSuggesstions = Product::where('workspace_id', $workspaceId)
                    ->where(function ($subQuery) use ($categoryIds, $productIds){
                        $subQuery->whereIn('category_id', $categoryIds);
                        $subQuery->orWhereIn('id', $productIds);
                    })
                    ->where('active', 1)
                    ->orderBy('order', 'asc')
                    ->get();

                $productSuggesstions->transform(function ($productItem) {
                    /** @var \App\Models\Product $item */
                    return $productItem->getFullInfo();
                });
            }

            // Get setting and check levering
            if ($cart) {
                // Check levering
                if (isset($cart->cartItems) && $cart->cartItems->count() > 0) {
                    $cart->total_price; // Call this attribute to get other attribute
                    $priceDiscount         = $cart->price_discount;
                    $isDeleveringAvailable = $cart->is_delevering_available;
                    $productIds            = $cart->product_ids;
                    $totalCouponDiscount   = $cart->total_coupon_discount;
                    $discountProducts      = $cart->discount_products;
                    $couponDiscount        = $cart->coupon_discount;
                }

                // Check distance
                if (request()->get('tab') === \App\Models\Cart::TAB_LEVERING
                    && (empty($cart->group_id))
                ) {
                    $condition = $this->workspaceRepository->getDeliveryConditions($workspaceId, [
                        'lat'  => $cart->lat,
                        'lng'  => $cart->long,
                    ])->first();

                    if (!$condition) {
                        session()->flash('address_not_avaliable', 1);
                    } elseif (!$isDeleveringAvailable) {
                        session()->flash('product_not_avaliable', 1);
                    }

                    if (!$condition || $condition->price_min > $cart->sub_total_price) {
                        $isDeleveringPriceMin = FALSE;
                    }

                    if ($condition && $cart->sub_total_price >= $condition->free) {
                        $isShowMessageFree = TRUE;
                    }
                }

                // Get setting Tijdslots - Bestellen tot maximaal
                if ($cart->workspace && $cart->workspace->settingTimeslots) {
                    $settingTimeslots = $cart->workspace->settingTimeslots;
                    $timeSlot         = $settingTimeslots->where('type', $cart->type)->first();
                    $now              = Carbon::now();
                    $startDate        = $now->format("Y-m-d");
                    $endDate          = $now->addDays($offsetDayOrder)->format("Y-m-d");

                    $timeslotDetails   = $this->settingTimeslotDetailRepository->with(['settingTimeslot'])->findWhere([
                        'workspace_id' => $workspaceId,
                        'type'         => $cart->type,
                        ['date', '>=', $startDate],
                        ['date', '<=', $endDate]
                    ]);

                    $timeslotHelper = new TimeslotHelper();

                    // Group timeslots by date
                    $timeslotDetails = $timeslotHelper->groupTimeslotsByDate($timeslotDetails);

                    /**
                     * @var string $date Date string
                     * @var array $listTime Array of timeslot details
                     */
                    foreach ($timeslotDetails as $date => $listTime) {
                        if (!$timeslotHelper->validateTimeslotDetails($listTime)) {
                            $datesDisable[] = $date;
                        }
                    }

                    $listDayAvalidable = array_map('intval', explode(',', $timeSlot->max_days));
                    $timeSlot          = $timeSlot->toArray();
                }
            }

            if (request()->get('reopen') === "modal-fill-address") {
                session()->flash('modal_fill_address', 1);
            }

            // Get redeem & validate redeem
            if (!auth()->guest()) {
                $user = auth()->user();
                $redeem = $this->loyaltyRepository->getRedeemByUser($workspaceId, $user);
                if ($redeem) {
                    $productPrices = Helper::calculatePriceFromCart($cart, $redeem->reward->products->pluck('id')->toArray());
                    $totalRedeemDiscount = Helper::calculateRedeemDiscountValue($redeem->reward, $productPrices);
                    $infoRedeem = $this->rewardRepository->validateRewardProducts($redeem->reward_level_id, $productIds);
                    $redeem     = $redeem->getFullInfo();

                    foreach ($infoRedeem as $pId => $value) {
                        if ($value == true) {
                            $discountProducts[] = $pId;
                        }
                    }
                }
            }

            $view->with([
                'cart'                  => $cart,
                'productSuggesstions'   => count($productSuggesstions) > 0 ? $productSuggesstions : array(),
                'coupons'               => $coupons,
                'workspaceId'           => $workspaceId,
                'conditionDelevering'   => $condition,
                'userId'                => $userId,
                'isDeleveringAvailable' => $isDeleveringAvailable,
                'isDeleveringPriceMin'  => $isDeleveringPriceMin,
                'isShowMessageFree'     => $isShowMessageFree,
                'offsetDayOrder'        => $offsetDayOrder,
                'offsetTimeOrder'       => $offsetTimeOrder,
                'priceDiscount'         => $priceDiscount,
                'holidays'              => $holidays,
                'openHours'             => $openHours,
                'dayInWeekActive'       => $dayInWeekActive,
                'timeSlot'              => $timeSlot,
                'listDayAvalidable'     => $listDayAvalidable,
                'datesDisable'          => $datesDisable,
                'infoRedeem'            => collect($infoRedeem),
                'redeem'                => $redeem,
                'type'                  => $cart ? $cart->type : $type,
                'totalCouponDiscount'   => $totalCouponDiscount,
                'discountProducts'      => $discountProducts,
                'totalRedeemDiscount'   => $totalRedeemDiscount,
                'couponDiscount'        => $couponDiscount,
                'groupDiscount'         => $groupDiscount,
                'failedOpties'          => $failedOpties,
            ]);
        } catch (\Exception $ex) {
            $view->with([
                'cart'                  => $cart,
                'productSuggesstions'   => count($productSuggesstions) > 0 ? $productSuggesstions : array(),
                'coupons'               => $coupons ?? [],
                'workspaceId'           => $workspaceId,
                'conditionDelevering'   => $condition,
                'userId'                => $userId,
                'isDeleveringAvailable' => $isDeleveringAvailable,
                'isDeleveringPriceMin'  => $isDeleveringPriceMin,
                'isShowMessageFree'     => $isShowMessageFree,
                'offsetDayOrder'        => $offsetDayOrder ?? NULL,
                'offsetTimeOrder'       => $offsetTimeOrder ?? NULL,
                'priceDiscount'         => $priceDiscount,
                'holidays'              => $holidays ?? [],
                'openHours'             => $openHours ?? [],
                'dayInWeekActive'       => $dayInWeekActive ?? [],
                'timeSlot'              => $timeSlot,
                'listDayAvalidable'     => $listDayAvalidable,
                'datesDisable'          => $datesDisable,
                'infoRedeem'            => collect($infoRedeem),
                'redeem'                => $redeem,
                'type'                  => $cart ? $cart->type : $type,
                'totalCouponDiscount'   => $totalCouponDiscount,
                'discountProducts'      => $discountProducts,
                'totalRedeemDiscount'   => $totalRedeemDiscount,
                'couponDiscount'        => $couponDiscount,
                'groupDiscount'         => $groupDiscount,
                'failedOpties'          => $failedOpties,
            ]);
        }
    }
}
