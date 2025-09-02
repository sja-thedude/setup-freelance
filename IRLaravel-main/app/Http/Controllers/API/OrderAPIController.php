<?php

namespace App\Http\Controllers\API;

use App\Facades\Helper;
use App\Http\Requests\API\CreateOrderAPIRequest;
use App\Http\Requests\API\UpdateOrderAPIRequest;
use App\Http\Requests\API\UpdateOrderPaymentAPIRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */
class OrderAPIController extends AppBaseController
{
    /**
     * @var OrderRepository $orderRepository
     */
    protected $orderRepository;

    /**
     * OrderAPIController constructor.
     * @param OrderRepository $orderRepo
     */
    public function __construct(OrderRepository $orderRepo)
    {
        parent::__construct();

        $this->orderRepository = $orderRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page', 15);
        $limit = (int)$request->get('limit', $perPage);
        $orders = $this->orderRepository->adminGetListOrders($request, $limit);

        return $this->sendResponse($orders, trans('order.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateOrderAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateOrderAPIRequest $request)
    {
        $input = $request->all();
        $userLoggedIn = auth()->user();

        // Get Timezone from request
        $appAppTimezone = Helper::getAppTimezone($request);

        if (!empty($appAppTimezone)) {
            $input['timezone'] = $appAppTimezone;
        }

        // Get template (workspace) by App-Token from request
        $template = Helper::getWorkspaceFromAppToken($request);

        if (!empty($template)) {
            $input['template_id'] = $template->id;
        }

        // Get group restaurant by Group-Token from request
        $groupRestaurant = Helper::getGroupRestaurantFromGroupToken($request);
        if (!empty($groupRestaurant)) {
            $input['group_restaurant_id'] = $groupRestaurant->id;
        }

        try {
            // Case test account
            if (!empty($userLoggedIn) && $userLoggedIn->isSuperAdmin()) {
                $input['is_test_account'] = Order::IS_TEST_ACCOUNT;
                $input['status'] = Order::PAYMENT_STATUS_PAID;
            }

            $order = $this->orderRepository->create($input);

            // Case test account
            if (!empty($userLoggedIn) && $userLoggedIn->isSuperAdmin()) {
                $order->total_paid = $order->total_price;
                $order->payed_at = now();
                $order->save();
            }
        } catch (\Throwable $ex) {
            $data = [
                'code' => $ex->getCode(),
            ];
            return $this->sendError($ex->getMessage(), 500, $data);
        }

        // Reload order with relations
        /** @var Order $order */
        $order = $this->orderRepository
            ->with([
                'workspace',
                /* Order items */
                'orderItems',
                /* Product info */
                'orderItems.product',
                'orderItems.product.translations',
                'orderItems.product.workspace',
                'orderItems.product.category',
                'orderItems.product.category.translations',
                'orderItems.product.vat',
                'orderItems.product.productLabels',
                'orderItems.product.productAvatar',
                /* Order options items */
                'orderItems.optionItems',
                /* Product option item info */
                'orderItems.optionItems.optionItem',
                /* Product option info */
                'orderItems.optionItems.optionItem.option',
                'orderItems.optionItems.optionItem.option.workspace',
                'orderItems.optionItems.optionItem.option.translations',
            ])
            ->withCount(['orderItems'])
            ->findWithoutFail($order->id);

        // Not found
        if (empty($order)) {
            return $this->sendError(trans('order.not_found'));
        }

        $result = $order->getFullInfo();

        return $this->sendResponse($result, trans('order.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Reload order with relations
        /** @var Order $order */
        $order = $this->orderRepository
            ->with([
                'workspace',
                /* Order items */
                'orderItems',
                /* Product info */
                'orderItems.product',
                'orderItems.product.translations',
                'orderItems.product.workspace',
                'orderItems.product.category',
                'orderItems.product.category.translations',
                'orderItems.product.vat',
                'orderItems.product.productLabels',
                'orderItems.product.productAvatar',
                /* Order options items */
                'orderItems.optionItems',
                /* Product option item info */
                'orderItems.optionItems.optionItem',
                /* Product option info */
                'orderItems.optionItems.optionItem.option',
                'orderItems.optionItems.optionItem.option.workspace',
                'orderItems.optionItems.optionItem.option.translations',
            ])
            ->findWithoutFail($id);

        // Not found
        if (empty($order)) {
            return $this->sendError(trans('order.not_found'));
        }

        $result = $order->getFullInfo();

        return $this->sendResponse($result, trans('order.message_retrieved_successfully'));
    }

    /**
     * @param UpdateOrderAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOrderAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Order $order */
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.not_found'));
        }

        $order = $this->orderRepository->update($input, $id);

        return $this->sendResponse($order->toArray(), trans('order.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Order $order */
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.not_found'));
        }

        // $order->delete();

        return $this->sendResponse($id, trans('order.message_deleted_successfully'));
    }

    /**
     * @param UpdateOrderPaymentAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updatePayment(UpdateOrderPaymentAPIRequest $request, $id)
    {
        $input = $request->only(['payment_method', 'payment_status', 'total_paid']);

        $this->orderRepository->updatePaymentMethod($input, $id);

        // Reload order with relations
        /** @var Order $order */
        $order = $this->orderRepository
            ->with([
                'workspace',
                /* Order items */
                'orderItems',
                /* Product info */
                'orderItems.product',
                'orderItems.product.translations',
                'orderItems.product.workspace',
                'orderItems.product.category',
                'orderItems.product.category.translations',
                'orderItems.product.vat',
                'orderItems.product.productLabels',
                'orderItems.product.productAvatar',
                /* Order options items */
                'orderItems.optionItems',
                /* Product option item info */
                'orderItems.optionItems.optionItem',
                /* Product option info */
                'orderItems.optionItems.optionItem.option',
                'orderItems.optionItems.optionItem.option.workspace',
                'orderItems.optionItems.optionItem.option.translations',
            ])
            ->findWithoutFail($id);

        // Not found
        if (empty($order)) {
            return $this->sendError(trans('order.not_found'));
        }

        $result = $order->getFullInfo();

        return $this->sendResponse($result, trans('order.message_updated_successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $request->merge([
            'user_id' => $request->user()->id,
            'hide_parent' => true,
            'in_statuses' => [
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PAID,
            ],
        ]);

        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $orders = $this->orderRepository->paginate($limit);


        $orders->transform(function ($item) {
            /** @var Order $item */
            return $item->getListItemInfo();
        });
        $result = $orders->toArray();

        return $this->sendResponse($result, trans('order.message_retrieved_list_successfully'));
    }

    /**
     * Cancel a order
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order)
    {
        $status = Order::PAYMENT_STATUS_CANCELLED;
        $this->orderRepository->changeStatus($order, $status);

        return $this->sendResponse(null, trans('order.message_changed_status_successfully'));
    }

    public function deeplinkConfiguration(Request $request) {
        $config = Helper::getMobileConfig($request);
        $deeplink = [
            'android' => array_get($config, 'android.deeplink'),
            'ios' => array_get($config, 'ios.deeplink')
        ];

        $download = [
            'android' => config('mobile.android.download'),
            'ios' => config('mobile.ios.download')
        ];

        return $this->sendResponse(compact('deeplink', 'download'), trans('messages.success'));
    }
}
