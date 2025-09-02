<?php

namespace App\Http\Controllers\Frontend;

use App\Facades\Helper;
use App\Helpers\Order as OrderHelper;
use App\Models\Group;
use App\Repositories\OrderRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;

/**
 * Class OrderController
 *
 * @package App\Http\Controllers\Frontend
 */
class OrderController extends BaseController
{
    /**
     * @var OrderRepository
     */
    public $orderRepository;

    /**
     * @var WorkspaceRepository
     */
    public $workspaceRepository;

    /**
     * OrderController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param workspaceRepository $workspaceRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        WorkspaceRepository $workspaceRepository
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function show(Request $request, $id)
    {
        $order = $this->orderRepository
            ->with([
                'orderItems',
                'coupon',
                'user',
                'group',
                'workspace.workspaceExtras',
                'workspace.settingPayments',
                'workspace.settingDeliveryConditions',
                'orderItems.product',
                'orderItems.optionItems',
                'orderItems.optionItems.option',
                'orderItems.optionItems.optionItem',
                'orderItems.category.productSuggestions.product',
            ])
            ->findWhere(['id' => $id])
            ->first();

        $order = OrderHelper::sortOptionItems($order);
        $workspace = $order->workspace;
        $general = $workspace->getSettingGeneral();

        // Check levering
        $isDeleveringAvailable = TRUE;
        $isDeleveringPriceMin  = TRUE;
        $conditionDelevering   = NULL;
        if ($order) {
            if (isset($order->orderItems) && $order->orderItems->count() > 0) {
                foreach ($order->orderItems as $item) {
                    $metas    = json_decode($item->metas);
                    $category = $metas->category;
                    if (!$category->available_delivery) {
                        $isDeleveringAvailable = FALSE;
                        break;
                    }
                }
            }

            // Check distance
            if ((int) $order->type === \App\Models\Cart::TYPE_LEVERING) {
                $conditionDelevering = $this->workspaceRepository->getDeliveryConditions($workspace->id, [
                    'lat'  => $order->lat,
                    'lng'  => $order->lng,
                ])->first();

                if (!$conditionDelevering || $conditionDelevering->price_min > $order->subtotal) {
                    $isDeleveringPriceMin = FALSE;
                }
            }
        }

        //Show detail using ajax popup
        if($request->ajax()) {
            $order = OrderHelper::convertOrderItem($order);
            $html = \View::make($this->guard . '.partials.order-detail')->with(compact(
                'order',
                'workspace'
            ))->render();

            $groupId = $order->group_id;
            $groupInactiveHtml = '';
            if ($groupId) {
                $group = Group::find($groupId);
                if ($group && !$group->active) {
                    $groupInactiveHtml = \View::make($this->guard . '.partials.popup-avoid-reorder')->render();
                }
            }
            
            return $this->sendResponse(["orderDetailHtml" => $html, "groupInactiveHtml" => $groupInactiveHtml], trans('messages.success'));
        }

        // Config for deeplink
        $config = Helper::getMobileConfig($request);
        $discountProducts = [];

        foreach ($order->orderItems as $orderItem) {
            if (isset($orderItem->available_discount) && !empty($orderItem->available_discount)) {
                $discountProducts[] = $orderItem->product_id;
            }
        }

        $couponDiscount = $order->coupon_discount??0;
        $redeemDiscount = $order->redeem_discount??0;
        $groupDiscount = $order->group_discount??0;

        return view('web.carts.success', compact(
            'config',
            'order',
            'workspace',
            'general',
            'conditionDelevering',
            'isDeleveringPriceMin',
            'isDeleveringAvailable',
            'couponDiscount',
            'redeemDiscount',
            'groupDiscount',
            'discountProducts'
        ));
    }
}