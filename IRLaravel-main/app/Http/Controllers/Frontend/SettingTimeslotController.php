<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Cart;
use App\Models\SettingTimeslotDetail;
use App\Repositories\CartRepository;
use App\Repositories\SettingTimeslotDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingTimeslotController extends BaseController
{
    /**
     * @var SettingTimeslotDetailRepository
     */
    public $settingTimeslotDetailRepository;

    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * SettingTimeslotController constructor.
     *
     * @param SettingTimeslotDetailRepository $settingTimeslotDetailRepository
     * @param CartRepository                  $cartRepository
     */
    public function __construct(
        SettingTimeslotDetailRepository $settingTimeslotDetailRepository,
        CartRepository $cartRepository
    ) {
        parent::__construct();

        $this->settingTimeslotDetailRepository = $settingTimeslotDetailRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request)
    {
        if ($request->groupId !== "undefined") {
            return response()->json([
                'code' => 200,
                'data' => ""
            ]);
        }

        $dateRoot     = base64_decode($request->date);
        $timeZone     = base64_decode($request->timezone);
        $workspaceId  = $request->workspaceId;
        $date         = date("Y-m-d", strtotime(str_replace('/', '-', $dateRoot)));

        // Check setting: Voorkeuren - Minimum wachttijd
        $dateTimeParse = Carbon::now($timeZone)->addMinutes((int) $request->offsetTimeOrder);
        $dateNow       = Carbon::now($timeZone)->format("Y-m-d");
        $dateStart     = $dateTimeParse->format("Y-m-d");
        $timeStart     = $dateTimeParse->format("H:i:s");

        $condition = [
            'date'         => $date,
            'type'         => $request->type,
            'workspace_id' => $workspaceId,
        ];

        if ($date === $dateNow) {
            if ($dateStart !== $date) {
                return response()->json([
                    'code' => 200,
                    'data' => ""
                ]);
            }

            // Check setting: Voorkeuren - Minimum wachttijd
            $condition[] = ['time', '>=', $timeStart];
        }

        // All time-slots by condition
        $settingTimeslot = SettingTimeslotDetail::with([
                'settingTimeslot' => function ($query) {
                    $query->select('id', 'max_price_per_slot');
                },
                'orders' => function ($query) {
                    $query->select('id', 'setting_timeslot_detail_id', 'total_price', 'status', 'payment_method');
                }
            ])
            ->where($condition)
            ->where('active', true)
            ->select('id', 'setting_timeslot_id', 'active', 'max', 'time')
            ->get();

        // Cart
        $cart = Cart::with([
                'cartItems' => function ($query) {
                    $query->with(['product', 'product.translations', 'product.vat', 'product.category', 'product.category.translations']);
                },
                'cartItems.cartOptionItems' => function ($query) {
                    $query->with(['optionItem']);
                }
            ])
            ->where(['id' => $request->get('cartId')])
            ->firstOrFail();
        $cartMoney = $cart->total_price;

        $htmlTime = "";
        $settingTimeslot = $settingTimeslot->chunk(12);

        foreach ($settingTimeslot as $blockTimes) {
            $arrBlockTimes = [];

            foreach ($blockTimes as $time) {
                $countMax = 0;
                $countMoney = $cartMoney;

                if (!empty($time->orders)) {
                    foreach ($time->orders as $order) {
                        $countMoney += $order->total_price;

                        if (($order->status == \App\Models\Order::PAYMENT_STATUS_PAID
                            || in_array($order->payment_method, [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]))) {
                            $countMax++;
                        }
                    }
                }

                $time->countMoney = $countMoney;
                $time->countMax = $countMax;
                $time->maxPricePerSlot = $time->settingTimeslot->max_price_per_slot;
                $time->timeDisplay = \Carbon\Carbon::parse($time->time)->format('H:i');

                $arrBlockTimes[] = $time;
            }

            $htmlTime .= view('web.carts.partials.time-step2', [
                'blockTimes' => $arrBlockTimes,
                'cart'       => $cart,
            ])->render();
        }

        return response()->json([
            'code' => 200,
            'data' => $htmlTime
        ]);
    }
}