<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Currently purely for debugging. This way we can easily show paid orders
 *
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class OrderController extends BaseController
{
    private $orderRepository;

    public function __construct(
        OrderRepository $orderRepo
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepo;
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function show(Request $request, $id)
    {
        echo '<h1>Order detail</h1>';

        $order = Order::where('id', (int) $id)
            ->first();

        echo '<h2>Order</h2>';

        dump(
            $order->attributesToArray()
        );

        if(!empty($order)) {
            echo '<h2>User</h2>';

            $orderUser = User::where('id', (int) $order->user_id)->first();
            dump(array_intersect_key($orderUser->attributesToArray(), array_flip(['id', 'email', 'last_login', 'gsm', 'phone'])));

            echo '<h2>Products</h2>';

            $orderItems = $order->orderItems()->get();

            echo '<ul>';

            foreach($orderItems as $orderItem) {
                echo '<li>';
                $product = $orderItem->product()->first();
                $productTranslations = $product->translations()->first();

                dump(
                    $orderItem->attributesToArray(),
                    $productTranslations->attributesToArray()
                );
                echo '</li>';
            }

            echo '</ul>';
        }
    }

    /**
     * Display a listing of the Order.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $workspaces = \App\Models\Workspace::all();

        // Default show of today to speed up.
        if(! $request->has('filter_datetime')) {
            $request->merge(['filter_datetime' => date('d/m/Y')]);
        }

        $orders = $this->orderRepository->adminGetListOrders($request);

        return view('admin.orders.index', compact('orders', 'workspaces'));
    }

    public function printItem(Request $request, $type, $orderId) {
        $order = $this->orderRepository->findWithoutFail($orderId);

        if($type == config('print.all_type.kassabon') && $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
            if(!$order->groupOrders->isEmpty()) {
                foreach($order->groupOrders as $subOrder) {
                    $view = $this->orderRepository->printItemByType($subOrder->id, $subOrder, $type);
                }
            }
        } else {
            $view = $this->orderRepository->printItemByType($orderId, $order, $type);
        }

        return $this->sendResponse(compact('view'), null);
    }
}