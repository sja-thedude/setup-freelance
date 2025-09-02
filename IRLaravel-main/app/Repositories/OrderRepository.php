<?php

namespace App\Repositories;

use App\Helpers\GroupHelper;
use App\Helpers\Helper;
use App\Facades\Helper as HelperFacade;
use App\Jobs\PushOrderToConnectors;
use App\Jobs\SendEmailSuccessOrder;
use App\Models\Contact;
use App\Models\OptionItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReference;
use App\Models\Product;
use App\Models\RedeemHistory;
use App\Models\Reward;
use App\Models\SettingDeliveryConditions;
use App\Models\SettingPreference;
use App\Models\SettingPrint;
use App\Models\SettingPayment;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceExtra;
use Carbon\Carbon;
use App\Helpers\Order as OrderHelper;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'workspace_id',
        'user_id',
        'setting_payment_id',
        'open_timeslot_id',
        'group_id',
        'coupon_id',
        'daily_id',
        'payment_method',
        'payment_status',
        'coupon_code',
        'date_time',
        'time',
        'date',
        'address',
        'address_type',
        'type',
        'meta_data',
        'note',
        'total_price',
        'currency',
        'is_test_account',
        'auto_print',
        'run_crontab',
        'auto_print_sticker',
        'auto_print_werkbon',
        'auto_print_kassabon',
        'trigger_auto_scan'
    ];
    
    /**
     * Configure the Model
     */
    public function model()
    {
        return Order::class;
    }

    /**
     * @param $request
     * @param $workspaceId
     * @param  bool  $print
     * @param  array  $cond
     * @param  null  $perPage
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getListOrders($request, $workspaceId, $print = false, $cond = [], $perPage = null)
    {
        $input = $request->all();
        $model = $this->makeModel()->where('workspace_id', $workspaceId);
        $now = now();
        $timezone = !empty($input['timezone']) ? $input['timezone'] : 'UTC';
        $rangeStartDate = $request->get('range_start_date', null);
        $rangeEndDate = $request->get('range_end_date', null);
        $keywordSearch = $request->get('keyword_search', null);
        $filterTransformType = $request->get('filter_transform_type', null);
        $filterPaymentMethod = $request->get('filter_payment_method', null);
        $filterOrderType = $request->get('filter_order_type', null);
        $filterTypeDateTime = $request->get('filter_type_datetime', null);
        $isBuyYourSelf = in_array($filterTransformType, \App\Helpers\OrderHelper::buyYourSelfTypes());
        
        if (empty($rangeStartDate) && empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        }
        if (!empty($rangeStartDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeStartDate, $timezone, 0);
        }
        if (!empty($rangeEndDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeEndDate, $timezone, 1);
        }
        
        if (!empty($keywordSearch)) {
            $labelTableNumber = trans('order.label_table_number');
            $labelTableNumberUpper = strtoupper($labelTableNumber);
            $keywordSearchUpper = strtoupper($keywordSearch);
            // If the manager searches for example "TAFEL 42" in the search field,
            // he will only see this table's order(s).
            if (str_contains($keywordSearchUpper, $labelTableNumberUpper)) {
                $tableNumber = trim(str_replace($labelTableNumberUpper, '', $keywordSearchUpper).'');
                
                if (!empty($tableNumber) && is_numeric($tableNumber)) {
                    $model = $model
                        ->where('type', \App\Helpers\OrderHelper::TYPE_IN_HOUSE)
                        ->where('table_number', (int) $tableNumber);
                }
            } else {
                $model = $model->where(function ($query) use ($keywordSearch)
                {
                    $query->whereHas('group', function ($subQuery) use ($keywordSearch)
                    {
                        $subQuery->where('name', 'LIKE', '%'.$keywordSearch.'%');
                    })
                        ->orWhereHas('user', function ($subQuery) use ($keywordSearch)
                        {
                            $subQuery->where('name', 'LIKE', '%'.$keywordSearch.'%');
                        })
                        ->orWhereHas('contact', function ($subQuery) use ($keywordSearch)
                        {
                            $subQuery->where('name', 'LIKE', '%'.$keywordSearch.'%');
                        });
                });
            }
        }
        
        if (!is_null($filterTransformType)) {
            $model = $model->where(function ($query) use ($filterTransformType)
            {
                $query->where('type', $filterTransformType)
                    ->orWhereHas('group', function ($subQuery) use ($filterTransformType)
                    {
                        $subQuery->where('type', $filterTransformType);
                    });
            });
            
            if (in_array($filterTransformType, [\App\Helpers\OrderHelper::TYPE_IN_HOUSE])) {
                // If the "Ter plaatse" filter is on,
                // the order list will be sorted on payment status first (ascending -> paid orders are at the bottom)
                // and created time (descending).
                // and then table number asc.
                $model = $model->orderBy('status', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->orderBy('table_number', 'asc');
            }
        }
        
        if (!is_null($filterPaymentMethod)) {
            $methodConvert = config('common.payment_method_convert.'.$filterPaymentMethod);
            $model = $model->where(function ($query) use ($methodConvert)
            {
                $query->whereIn('payment_method', $methodConvert)
                    ->orWhereHas('groupOrders', function ($subQuery) use ($methodConvert)
                    {
                        $subQuery->whereIn('payment_method', $methodConvert);
                    });
            });
        }
        if (!is_null($filterOrderType)) {
            if (!empty($filterOrderType)) {
                $model = $model->where(function ($query)
                {
                    $query->whereNotNull('group_id')->orWhere('group_id', '!=', '');
                });
            } else {
                $model = $model->where(function ($query)
                {
                    $query->whereNull('group_id')->orWhere('group_id', '=', '');
                });
            }
        }
        if (!is_null($filterTypeDateTime)) {
            if (!empty($filterTypeDateTime)) {
                $model = $model->where('date_time', '<=', $now);
            } else {
                $model = $model->where('date_time', '>', $now);
            }
        }
        $countSuccess = '(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NULL )+(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NOT NULL AND children.status = '.Order::PAYMENT_STATUS_PAID.' )';
        $model = $model->where(function ($query)
        {
            $query->where(function ($subQuery)
            {
                $subQuery->whereNull('group_id')->where('type', '!=', Order::TYPE_IN_HOUSE);
            })->orWhere(function ($subQuery)
            {
                $subQuery->whereNull('group_id')->where('type', Order::TYPE_IN_HOUSE)->whereNull('parent_id');
            })->orWhere(function ($subQuery)
            {
                $subQuery->whereNotNull('group_id')->whereNull('parent_id');
            });
        })
        ->addSelect([
            \DB::raw('(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id) AS children_orders_count'),
            \DB::raw('(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NOT NULL AND children.status <> ' . Order::PAYMENT_STATUS_PAID . ') AS children_failed_count'),
            \DB::raw($countSuccess . ' AS children_success_count'),
            'orders.*'
        ])
        ->where(function($query) use ($countSuccess) {
            $query->where(function($query) use ($countSuccess) {
                $query->where('type', Order::TYPE_IN_HOUSE)->whereRaw(\DB::raw($countSuccess . ' > 0'));
            })->orWhere('type', '!=', Order::TYPE_IN_HOUSE);
        });
        
        $model = OrderHelper::conditionShowOrderList($model);
        
        if (!empty($cond)) {
            $model = $model->where($cond['field'], $cond['cond'], $cond['value']);
        }
        
        $model = $model->with([
            'user',
            'group',
            //'parentOrder',
            'orderItems',
            'orderItems.product',
            'orderItems.product.category',
            'orderItems.product.options',
            'orderItems.optionItems',
            'orderItems.optionItems.optionItem',
        ])->get();
        
        if (!$isBuyYourSelf) {
            $model = $model->sortByDesc('gereed');
        }
        
        if ($perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageResults = $model->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();
            $results = new LengthAwarePaginator($currentPageResults, count($model), $perPage);
            $model = $results->setPath($request->url());
        }
        
        $model = $this->sortOrderOptionItem($model);
        
        if (!empty($print)) {
            return $model;
        }
        
        return $this->convertOrderList($model);
    }

    public function getOrderListForExport($request, $workspaceId)
    {
        $input = $request->all();
        $model = $this->makeModel()->where('orders.workspace_id', $workspaceId);
        $rangeStartDate = $request->get('range_start_date', null);
        $rangeEndDate = $request->get('range_end_date', null);
        $timezone = !empty($input['timezone']) ? $input['timezone'] : 'UTC';

        if (empty($rangeStartDate) && empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        }
        if (!empty($rangeStartDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeStartDate, $timezone, 0);
        }
        if (!empty($rangeEndDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeEndDate, $timezone, 1);
        }
        return $model->get();
    }

    public function getCustomersListForExport($workspaceId, $rangeStartDate = null, $rangeEndDate = null, $timezone = 'UTC')
    {
        $model = User::distinct()->select(['users.*'])
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.workspace_id', $workspaceId)
        ;

        if (empty($rangeStartDate) && empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        }
        if (!empty($rangeStartDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeStartDate, $timezone, 0);
        }
        if (!empty($rangeEndDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $rangeEndDate, $timezone, 1);
        }
        return $model->get();
    }

    public function sortOrderOptionItem($orders)
    {
        $orders->transform(function ($order)
        {
            return OrderHelper::sortOptionItems($order);
        });
        
        return $orders;
    }
    
    public function convertOrderList($orders)
    {
        $orders->transform(function ($order, $key)
        {
            $order = OrderHelper::sortByCategoryProduct($order);
            $order = OrderHelper::convertOrderItem($order);
            $order->print_class = $this->getPrintClass($order, $order->gereed);
            
            return $order;
        });
        
        return $orders;
    }
    
    /**
     * Get print class
     *
     * @param  Order  $order
     * @param  mixed  $inputDateTime
     * @return string
     */
    public function getPrintClass(Order $order, $inputDateTime)
    {
        $now = Carbon::now();
        $orderDateTime = Carbon::parse($inputDateTime);
        $diffSeconds = $now->diffInSeconds($orderDateTime);
        $diff = $diffSeconds / 60;
        $printClass = 'print-status-normal';
        
        if (!empty($order->printed_werkbon)) {
            $printClass = 'print-status-success';
        } else {
            if ($orderDateTime > $now) {
                if ($diff <= 10) {
                    $printClass = 'print-status-danger';
                }
            } else {
                // $orderDateTime <= $now
                if ($order->isBuyYourSelf()) {
                    // Because buy your self order is the time order
                    if ($diff > 10) {
                        $printClass = 'print-status-danger';
                    }
                } else {
                    $printClass = 'print-status-danger';
                }
            }
        }
        
        return $printClass;
    }
    
    public function getOptionFromSettingPreference($workspaceId)
    {
        return SettingPreference::where('workspace_id', $workspaceId)->first();
    }
    
    public function getAutoFromSettingPrint($workspaceId, $type)
    {
        return SettingPrint::where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->where('auto', true)
            ->first();
    }
    
    /**
     * Retrieve all data of repository, paginated
     *
     * @overwrite
     * @param  int|null  $limit
     * @param  array  $columns
     * @param  string  $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        
        $this->scopeQuery(function ($model) use ($request)
        {
            /** @var \Illuminate\Database\Eloquent\Builder $model */
            
            /** @var \App\Models\Product $assocModel */
            $assocModel = $model->getModel();
            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);
            
            // Prevent duplicate field
            $model = $model->select('orders.*')
                // with relations
                ->with(['workspace', 'user', 'group'])
                ->withCount('orderItems');
            
            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int) $request->get('workspace_id');
                
                $model = $model->where('workspace_id', $workspaceId);
            }
            
            // Filter by user
            if ($request->has('user_id')) {
                $userId = (int) $request->get('user_id');
                
                $model = $model->where('user_id', $userId);
            }
            
            // Filter by parent_id
            if ($request->get('hide_parent')) {
                $model = $model->where(function ($query)
                {
                    // Individual when group_id is null
                    // Group order when parent_id is null
                    $query->whereNull('group_id')
                        ->orWhereNotNull('parent_id');
                });
            }
            
            // Filter by order status
            if ($request->has('in_statuses') && !empty($request->get('in_statuses'))) {
                $inStatuses = $request->get('in_statuses');
                $model = $model->whereIn('orders.status', $inStatuses);
            }
            
            // Order by from request
            if (!empty($orderBy)) {
                // Order by main table
                $model = $model->orderBy($assocModel->getTable().'.'.$orderBy, $sortBy);
            } else {
                // Default order by
                $model = $model->orderBy($assocModel->getTable().'.created_at', 'desc');
            }
            
            return $model;
        });
        
        return parent::paginate($limit, $columns, $method);
    }
    
    /**
     * Override from parent to fix duplicate fire event when save 2 times
     *
     * @overwrite
     * @param  array  $attributes
     * @return mixed
     * @throws \Throwable
     */
    public function create(array $attributes)
    {
        $orderType = array_get($attributes, 'type');
        
        if (!empty($attributes['created_at'])) {
            unset($attributes['created_at']);
        }
        if (!empty($attributes['updated_at'])) {
            unset($attributes['updated_at']);
        }
        
        /*--------------------------------- Get date_time by timezone ---------------------------------*/
        
        $datetime = array_get($attributes, 'date_time');
        
        // Convert date_time to UTC from timezone
        $timezone = config('app.timezone');
        
        if (empty($attributes['date'])) {
            throw new \Exception(trans('order.message_zero_timeslot'), ERROR_ORDER_ZERO_TIMESLOT);
        }
        
        if (!empty($attributes['timezone'])) {
            $timezone = $attributes['timezone'];
            $systemTimezone = config('app.timezone');
            $fixedTimezone = config('app.timezone_fixed');
            
            if (!empty($fixedTimezone)) {
                // Suggestion by Kurt Aerts 25/06/2021
                // Ticket: https://vitex1.atlassian.net/browse/ITR-347
                // What i would do is:
                // Simply configure the timezone of a restaurant on restaurant level and then always use that timezone not the timezone of the user.
                // If the user has a different timezone we could show a warning. With restaurant is using timezone X you are using timezone Y.
                // The chance that someone will be ordering from US or somewhere else is quite small.
                $timezone = $fixedTimezone;
            }
            
            // Convert to the system timezone before save to database
            $datetime = Carbon::parse($datetime, $timezone)
                ->tz($systemTimezone)
                ->toDateTimeString();
        }
        
        $attributes['timezone'] = $timezone;
        $attributes['date_time'] = $datetime;
        $arrDateTime = explode(' ', $datetime);
        
        if (count($arrDateTime) != 2) {
            throw new \Exception('Invalid date_time');
        }
        
        $attributes['date'] = $arrDateTime[0];
        $attributes['time'] = $arrDateTime[1];
        
        /*--------------------------------- /Get date_time by timezone ---------------------------------*/
        
        // Validate workspace (restaurant) which you order
        $workspaceId = (int) array_get($attributes, 'workspace_id');
        /** @var \App\Models\Workspace $workspace */
        $workspace = Workspace::where('id', $workspaceId)->first();
        
        if (empty($workspace)) {
            throw new \Exception(trans('workspace.not_found'), 404);
        }
        
        // When restaurant is offline
        if (!$workspace->active || !$workspace->is_online) {
            throw new \Exception(trans('messages.workspace_offline'), 500);
        }
        
        // Validate Validate timeslot detail when order
        if (array_key_exists('setting_timeslot_detail_id',
                $attributes) && !empty($attributes['setting_timeslot_detail_id'])) {
            $settingTimeslotDetailId = (int) array_get($attributes, 'setting_timeslot_detail_id');
            $validTimeslot = $this->validateTimeslotDetail($settingTimeslotDetailId);
            
            if (!$validTimeslot) {
                throw new \Exception(trans('order.message_invalid_timeslot'), ERROR_ORDER_INVALID_TIMESLOT);
            }
        }
        
        // Parent order is all order in a group today
        $isGroup = !empty(array_get($attributes, 'group_id'));
        $groupId = (int) array_get($attributes, 'group_id');
        
        if (array_key_exists('parent_id', $attributes)) {
            // Prevent transfer parent_id from request
            unset($attributes['parent_id']);
        }
        
        if ($isGroup) {
            // Validate the cut-off time
            // When users are in Step 3 of the Cart flow after the Cut-off time of the selected group,
            // they must be blocked from submitting the payment method in the Step 3.
            // If the moment they submit a payment method (Mollie, Cash or For invoice)
            // in step 3 is later than the cut-off time of the group,
            // they will see an error message and be directed back to the step 2.
            
            /** @var \App\Models\Group|null $group */
            $group = \App\Models\Group::whereId($groupId)->first();
            
            // Invalid group
            if (empty($group)) {
                throw new \Exception(trans('group.not_found'), 404);
            }
            
            if (!$group->active) {
                throw new \Exception(trans('group.inactive'), 500);
            }
            
            if (!empty($group->close_time)) {
                // Original date/time from request
                $tmpRequestDatetime = Carbon::now($timezone);
                $requestDatetime = Carbon::parse($tmpRequestDatetime->toDateTimeString());
                $cutoffTime = Carbon::parse($attributes['date'].' '.$group->close_time);
                
                // overdue cutoff time
                if ($requestDatetime->greaterThan($cutoffTime)) {
                    throw new \Exception(trans('order.message_overdue_cutoff_time'), ERROR_ORDER_OVERDUE_CUTOFF_TIME);
                }
            }
            
            // Get parent order
            /** @var \App\Models\Order $parent */
            $parent = Order::where('workspace_id', $workspaceId)
                ->where('group_id', $groupId)
                ->whereDate('date', array_get($attributes, 'date'))
                ->first();
            
            if (empty($parent)) {
                // Create parent order if not exist
                $parent = Order::create($attributes);
            }
            
            // Push parent to sub orders
            $attributes['parent_id'] = $parent->id;
        }
        
        if (in_array($orderType, \App\Helpers\OrderHelper::buyYourSelfTypes())) {
            $now = Carbon::now();
            $attributes = array_merge($attributes, [
                'date' => $now->toDateString(),
                'time' => $now->toTimeString(),
                'date_time' => $now->toDateTimeString(),
            ]);
            
            $contact = $this->upsertContact($workspaceId, $attributes, $orderType);
            
            if (!empty($contact)) {
                $attributes['contact_id'] = $contact->id;
            }
            
            // Create new contact for Order Table ordering
            if ($orderType === \App\Helpers\OrderHelper::TYPE_IN_HOUSE) {
                // Required table_number
                if (empty($attributes['table_number'])) {
                    throw new \Exception(trans('order.message_required_table_number'), 422);
                }
                
                if (empty($attributes['parent_id'])) {
                    $parent = $this->upsertParentOrder($workspaceId, $attributes, $orderType);
                    
                    if (!empty($parent)) {
                        // Push parent to sub orders
                        $attributes['parent_id'] = $parent->id;
                    }
                }
            }
        }
        
        // Create order item
        // If we catch an exception we'll rollback this transaction and try again if we
        // are not out of attempts. If we are out of attempts we will just throw the
        // exception back out and let the developer handle an uncaught exceptions.
        $model = \DB::transaction(function () use ($attributes, $orderType)
        {
            $model = parent::create($attributes);
            
            $listApplicableProducts = Helper::getApplicableProducts($attributes);
            if (array_key_exists('items', $attributes)) {
                $items = $attributes['items'];
                $model = $this->syncOrderItems($model, $items, $listApplicableProducts);
            }
            
            if (array_key_exists('setting_delivery_condition_id', $attributes)
                && !empty($attributes['setting_delivery_condition_id'])) {
                $deliveryId = (int) $attributes['setting_delivery_condition_id'];
                $model = $this->applyDeliveryFee($model, $deliveryId);
            }

            if (!in_array($orderType, \App\Helpers\OrderHelper::buyYourSelfTypes())) {
                $model = $this->applyServiceCost($model);
            }
            
            return $model;
        });
        
        /** @var \App\Models\Order $order */
        $order = $model->refresh();
        
        if (
            $order->status == Order::PAYMENT_STATUS_PAID
            || in_array($order->payment_method, [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR])
        ) {
            // Increment loyalty from order
            $this->incrementLoyalty($model);
            
            // Confirmed order
            $this->confirmedOrderSendMailAndPrint($order, false, isset($attributes['locale']) ? $attributes['locale'] : null);
            
            // Make paid order items
            $this->makePaidOrderItems($order);
        }
        
        // Cleanup old order
        if (!empty($attributes['old_order_id'])) {
            $oldOrderId = (int) $attributes['old_order_id'];
            
            \DB::transaction(function () use ($oldOrderId)
            {
                $this->delete($oldOrderId);
            });
        }
        
        $model->refresh();
        
        return $model;
    }
    
    public function confirmedOrderSendMailAndPrint(Order $order, $skipEmail = false, $userLocale = null)
    {
        $today = Carbon::today();
        $orderId = $order->id;
        
        if (!empty($order->group_id) && !empty($order->parent_id)) {
            $orderId = $order->parent_id;
        }
        
        if ($order->type == Order::TYPE_IN_HOUSE) {
            if(!empty($order->table_last_person)) {
                if (empty($order->group_id) && !empty($order->parent_id) && $order->type == Order::TYPE_IN_HOUSE) {
                    $orderId = $order->parent_id;
                }

                // print parent order
                \App\Facades\Order::autoPrintOrder(
                    [$orderId],
                    [SettingPrint::TYPE_WERKBON, SettingPrint::TYPE_STICKER],
                    false,
                    true,
                    null,
                    $userLocale
                );

                if(!$order->parentOrder->groupOrders->isEmpty()) {
                    foreach($order->parentOrder->groupOrders as $subOrder) {
                        if (!empty($subOrder->parent_id)) {
                            // print sub order
                            \App\Facades\Order::autoPrintOrder(
                                [$subOrder->id],
                                [SettingPrint::TYPE_KASSABON],
                                false,
                                true,
                                null,
                                $userLocale
                            );
                        }
                    }
                }
            }
        } else {
            // group order & individual & self ordering
            \App\Facades\Order::autoPrintOrder(
                [$orderId],
                [SettingPrint::TYPE_WERKBON, SettingPrint::TYPE_KASSABON, SettingPrint::TYPE_STICKER],
                false,
                true,
                null,
                $userLocale
            );
        }

        if(!empty($skipEmail)) {
            return true;
        }

        // Send mail order success
        $this->sendMailConfirm($order, null, $userLocale);
        
        // Send order confirmation to manager as well.
        // In case they have internet breakdown they still have access to the order confirmation.
        // If the order is created for today, the email is sent immediately.
        // If the order is created for days in the future, the email is sent at 00:15 on that date.
        if ($order->date_time->toDateString() == $today->toDateString()) {
            // Push order with one of the connectors
            dispatch(new PushOrderToConnectors($orderId, PushOrderToConnectors::TRIGGER_TYPE_AUTO));
            
            $this->sendMailConfirm($order, $order->workspace->user);
            
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
    
    /**
     * @overwrite
     * @param  array  $attributes
     * @param  int  $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, $id)
    {
        return parent::update($attributes, $id);
    }
    
    /**
     * @param  Order  $order
     * @param  array  $items
     * @return Order
     * @throws \Exception
     */
    public function syncOrderItems(Order $order, array $items, $listApplicableProducts = [])
    {
        if (empty($items)) {
            // Return empty collection
            return $order;
        }
        
        $subTotal = 0;
        $grandTotal = 0;
        $currency = null;
        
        foreach ($items as $arrItem) {
            $productId = (int) $arrItem['product_id'];
            /** @var \App\Models\Product $product */
            $product = Product::active()->find($productId);
            // Invalid product
            if (empty($product)) {
                throw new \Exception(trans('product.not_found')." #{$productId}", 422);
            }
            // Currency by first product
            if (empty($currency) && !empty($product->currency)) {
                $currency = $product->currency;
            }
            // Total price of item
            $quantity = (int) array_get($arrItem, 'quantity', 1);
            $totalPriceItem = $product->price;
            
            // BEGIN Get VAT value of product by order type -----------------------------
            $vatPercent = null;
            $vatProduct = $product->vat;
            
            if (!empty($vatProduct)) {
                if ($order->type == Order::TYPE_TAKEOUT) {
                    $vatPercent = $vatProduct->take_out;
                } elseif ($order->type == Order::TYPE_DELIVERY) {
                    $vatPercent = $vatProduct->delivery;
                } elseif ($order->type == Order::TYPE_IN_HOUSE) {
                    $vatPercent = $vatProduct->in_house;
                } elseif ($order->type == \App\Helpers\OrderHelper::TYPE_SELF_ORDERING) {
                    $vatPercent = $vatProduct->take_out;
                }
            }
            // END Get VAT value of product by order type -----------------------------
            
            $metas = [
                'category' => $product->category,
                'product' => $product
            ];
            
            // Custom product data
            $arrItem = array_merge($arrItem, [
                'workspace_id' => $order->workspace_id,
                'order_id' => $order->id,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'total_number' => $quantity,
                'type' => $order->type,
                'vat_percent' => $vatPercent,
                'metas' => json_encode($metas)
            ]);
            
            // Create a order_items record
            /** @var \App\Models\OrderItem $orderItem */
            $orderItem = $order->orderItems()->create($arrItem);
            
            if (array_key_exists('options', $arrItem)) {
                foreach ($arrItem['options'] as $arrOption) {
                    if (array_key_exists('option_items', $arrOption)) {
                        foreach ($arrOption['option_items'] as $arrOptionItem) {
                            $optionItemId = (int) $arrOptionItem['option_item_id'];
                            /** @var \App\Models\OptionItem $optionItem */
                            $optionItem = OptionItem::find($optionItemId);
                            
                            // Invalid product
                            if (empty($optionItem)) {
                                throw new \Exception(trans('option_item.not_found')." #{$optionItemId}", 422);
                            }
                            
                            $metas = [
                                'product' => $product,
                                'option' => $optionItem->option()->with('optionItems')->get(),
                                'option_item' => $optionItem
                            ];
                            
                            // Update order item price by product option items
                            $totalPriceItem += $optionItem->price;
                            $arrOptionItem = array_merge($arrOptionItem, [
                                'order_item_id' => $orderItem->getKey(),
                                'product_id' => $productId,
                                'optie_id' => $optionItem->opties_id,
                                'optie_item_id' => $optionItemId,
                                'price' => $optionItem->price,
                                'metas' => json_encode($metas)
                            ]);
                            
                            // Create a order_option_items record
                            /** @var \App\Models\OrderOptionItem $orderOptionItem */
                            $orderOptionItem = $orderItem->optionItems()->create($arrOptionItem);
                        }
                    }
                }
            }
            
            // Subtotal of item * quantity
            $totalPriceItem = $totalPriceItem * $quantity;
            $orderItem->subtotal = ($totalPriceItem > 0) ? $totalPriceItem : 0;
            $orderItem->total_price = ($totalPriceItem > 0) ? $totalPriceItem : 0;
            // Addition to order subtotal
            $subTotal += $orderItem->subtotal;
            
            //Add flag available_discount to check if this product is applicable or not
            if (count($listApplicableProducts) > 0
                && in_array($orderItem->product_id, $listApplicableProducts)
            ) {
                $orderItem->available_discount = 1;
            }
            
            // Get Coupon discount from coupon data
            if (array_key_exists('coupon_id', $arrItem) && !empty($arrItem['coupon_id'])) {
                $couponId = (int) $arrItem['coupon_id'];
                $discount = $arrItem['discount'] ?? 0;
                $orderItem = $this->applyCoupon($orderItem, $couponId, $discount);
                // Apply coupon to Order
                $order->coupon_id = $orderItem->coupon_id;
                $order->coupon_discount = $order->coupon_discount + $discount;
            }
            
            // Get Group discount
            if (GroupHelper::isGroupDiscountFromOrderItems($items, $order['group_id'] ?? null)) {
                $discount = $arrItem['discount'] ?? 0;
                $orderItem = $this->applyGroupDiscount($orderItem, $discount);
                $order->group_discount = $order->group_discount + $discount;
            }
            
            // Get Redeem discount from redeem history data
            if (array_key_exists('redeem_history_id', $arrItem) && !empty($arrItem['redeem_history_id'])) {
                $redeemId = (int) $arrItem['redeem_history_id'];
                $orderItem = $this->applyRedeem($orderItem, $redeemId, $arrItem['discount']);
                $discount = $arrItem['discount'] ?? 0;
                // Apply redeem to Order
                $order->redeem_history_id = $orderItem->redeem_history_id;
                $order->redeem_discount = $order->redeem_discount + $discount;
            }
            
            // Addition to order total price
            $grandTotal += $orderItem->total_price;
            // Save data for Order Item
            $orderItem->save();
        }
        
        // Update order
        // Prevent negative value
        $order->subtotal = ($subTotal > 0) ? $subTotal : 0;
        $order->total_price = ($grandTotal > 0) ? $grandTotal : 0;
        
        if ($order->total_price <= 0) {
            // Set order status is paid when total price = 0
            $order->status = Order::PAYMENT_STATUS_PAID;
            $order->total_paid = 0;
            $order->payed_at = \Carbon\Carbon::now();
        }
        
        $order->currency = $currency;
        // Save data for Order
        $order->save();
        
        // Save data failed
        if (empty($order) || empty($order->id)) {
            throw new \Exception('Internal exception');
        }
        
        // Reload record model and relations
        return $order->refresh();
    }
    
    /**
     * @overwrite
     * @param  array  $attributes
     * @param  int  $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updatePaymentMethod(array $attributes, int $id)
    {
        return $this->update($attributes, $id);
    }
    
    /**
     * Increment loyalty from order
     * @param  Order  $order
     * @return bool
     */
    public function incrementLoyalty(Order $order)
    {
        // If you've used loyalty, stop and don't repeat
        if ($order->loyalty_added || empty($order->user_id)) {
            return false;
        }
        
        $workspace = $order->workspace;
        $settingGeneral = $workspace->settingGeneral;
        $workspaceExtras = $workspace->workspaceExtras;
        $allowLoyalties = $workspaceExtras
                ->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                ->where('active', true)
                ->count() > 0;
        $instellingen = (!empty($settingGeneral)) && !empty($settingGeneral->instellingen)
            ? $settingGeneral->instellingen
            : config('loyalty.rewards.instellingen');
        
        $loyaltyAttributes = [
            'workspace_id' => $order->workspace_id,
            'user_id' => $order->user_id
        ];
        
        /** @var \App\Models\Loyalty $loyalty */
        $loyalty = \App\Models\Loyalty::firstOrNew($loyaltyAttributes);
        if (!is_null($instellingen) && $instellingen > 0 && $allowLoyalties) {
            $loyalty->point += floor($order->total_price / $instellingen);
        }
        
        $loyalty->save();
        
        // Update loyalty_added status
        $order->loyalty_added = Order::LOYALTY_ADDED;
        $order->save();
    }
    
    public function rollbackLoyalty($order) {
        $workspace = $order->workspace;
        $settingGeneral = $workspace->settingGeneral;
        $workspaceExtras = $workspace->workspaceExtras;
        $allowLoyalties = $workspaceExtras
                ->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                ->where('active', true)
                ->count() > 0;
        $instellingen = (!empty($settingGeneral)) && !empty($settingGeneral->instellingen)
            ? $settingGeneral->instellingen
            : config('loyalty.rewards.instellingen');

        $loyaltyAttributes = [
            'workspace_id' => $order->workspace_id,
            'user_id' => $order->user_id
        ];
        
        $loyalty = \App\Models\Loyalty::where($loyaltyAttributes)->first();

        if (!empty($loyalty) && !is_null($instellingen) && $instellingen > 0 && $allowLoyalties) {
            $loyalty->point = $loyalty->point - floor($order->total_price / $instellingen) > 0 ? $loyalty->point - floor($order->total_price / $instellingen) : 0;
            $loyalty->save();
        }
        
        // Update loyalty_added status
        $order->loyalty_added = false;
        $order->save();
    }
    
    /**
     * Apply redeem
     *
     * @param  OrderItem  $orderItem
     * @param  int  $couponId
     * @return OrderItem
     * @throws \Exception
     */
    protected function applyCoupon(OrderItem $orderItem, int $couponId, $discount = 0)
    {
        /** @var \App\Models\Coupon $coupon */
        $coupon = \App\Models\Coupon::where('id', $couponId)
            ->where('expire_time', '>', \Carbon\Carbon::now())
            ->first();
        
        // Invalid redeem
        if (empty($coupon)) {
            throw new \Exception(trans('coupon.message_code_invalid')." #{$couponId}", 422);
        }
        
        // Apply discount <= total price of order item
        // And prevent negative number in this product
        // Subtract price from discount
        $orderItem->total_price -= number_format($discount, 2);
        
        if ($orderItem->total_price < 0) {
            $orderItem->total_price = 0;
        }
        
        if ($coupon->discount_type == \App\Models\Coupon::DISCOUNT_PERCENTAGE && $coupon->percentage == 100) {
            $checkCouponProduct = \App\Models\CouponProduct::where('coupon_id', $couponId)
                ->where('product_id', $orderItem->product_id)
                ->first();
            
            if (!empty($checkCouponProduct)) {
                $orderItem->total_price = 0;
                $discount = $orderItem->subtotal;
            }
        }
        
        // Apply coupon to Order item
        $orderItem->coupon_id = $couponId;
        $orderItem->coupon_discount = $discount;
        
        return $orderItem;
    }
    
    /**
     * Apply redeem
     *
     * @param  OrderItem  $orderItem
     * @param  int  $redeemId
     * @return OrderItem
     * @throws \Exception
     */
    protected function applyRedeem(OrderItem $orderItem, int $redeemId, $discount)
    {
        /** @var \App\Models\RedeemHistory $redeem */
        $redeem = RedeemHistory::find($redeemId);
        
        // Invalid redeem
        if (empty($redeem)) {
            throw new \Exception(trans('reward.message_reward_validate_failed')." #{$redeemId}", 422);
        }
        
        if (empty($redeem->reward_data)) {
            return $orderItem;
        }
        
        $reward = new Reward($redeem->reward_data);
        
        // Only apply discount with Reward type is discount
        if (!empty($reward) && $reward->type == \App\Models\Reward::KORTING) {
            // Subtract price from discount
            $orderItem->total_price -= number_format($discount, 2);
            
            // Clear redeem
            $redeem->loyalty->last_reward_level_id = $redeem->loyalty->reward_level_id;
            $redeem->loyalty->reward_level_id = null;
            $redeem->loyalty->save();
        }
        
        // Apply redeem to Order item
        $orderItem->redeem_history_id = $redeemId;
        $orderItem->redeem_discount = $discount;
        
        return $orderItem;
    }
    
    /**
     * Restore last redeem of failed order
     *
     * @param  Order  $order
     * @return Order
     */
    public function restoreLastRedeem(Order $order)
    {
        if (empty($order->redeemHistory)) {
            return $order;
        }
        
        $loyalty = $order->redeemHistory->loyalty;
        
        if (empty($loyalty)) {
            return $order;
        }
        
        $loyalty->reward_level_id = $loyalty->last_reward_level_id;
        $loyalty->save();
        
        return $order;
    }
    
    /**
     * Apply delivery fee
     *
     * @param  Order  $order
     * @param  int  $deliveryId
     * @return Order
     * @throws \Exception
     */
    public function applyDeliveryFee(Order $order, int $deliveryId)
    {
        /** @var \App\Models\SettingDeliveryConditions $settingDeliveryCondition */
        $settingDeliveryCondition = SettingDeliveryConditions::where('id', $deliveryId)
            ->first();
        
        if (empty($settingDeliveryCondition)) {
            throw new \Exception(trans('delivery_condition.not_found')." #{$deliveryId}", 422);
        }
        
        $shipPrice = 0;
        
        // Free delivery
        if ($order->subtotal < $settingDeliveryCondition->free) {
            $shipPrice = $settingDeliveryCondition->price;
        }
        
        // Apply fee condition
        $order->ship_price = $shipPrice;
        $order->total_price += $shipPrice;
        $order->save();
        
        return $order;
    }
    
    /**
     * Send mail confirm order
     *
     * @param  Order  $order
     * @param  User|null  $toUser
     * @return bool
     */
    public function sendMailConfirm(Order $order, User $toUser = null)
    {
        $currentLang = app()->getLocale();
        $order = OrderHelper::sortOptionItems($order);
        $user = $order->user;
        $toUser = (!empty($toUser)) ? $toUser : $user;
        $workspace = $order->workspace;
        $lang = $user ? $user->getLocale() : ($order->contact ? $order->contact->locale : $workspace->language);
        $codeId = $order->code_with_prefix;
        $dateTimeLocal = Helper::convertDateTimeToTimezone($order->date." ".$order->time, $order->timezone);
        $dateTimeLocalParse = Carbon::parse($dateTimeLocal);
        $dateLocal = $dateTimeLocalParse->format("d/m/Y");
        $timeLocal = $dateTimeLocalParse->format("H:i");
        $contact = $order->contact;
        app()->setLocale($lang);
        
        $dataContent = [
            'isSendMail' => true,
            'isDeleveringAvailable' => true,
            'isDeleveringPriceMin' => true,
            'cart' => $order,
            'listItem' => $order->orderItems,
            'conditionDelevering' => $workspace->settingDeliveryConditions->first(),
            'totalPrice' => 0,
            'subject' => trans('mail.order_success.subject', ['code' => $codeId]),
            'content1' => trans('mail.order_success.content1'),
            'content4' => trans('mail.order_success.content4'),
            'content5' => trans('mail.reminder.content5'),
            'content6' => trans('cart.totaal'),
            'content7' => trans('cart.success_betaalstatus'),
            'content8' => trans('cart.success_betaalmethode'),
            'content9' => trans('cart.success_opmerkingen'),
            'content10' => trans('cart.groep'),
            'content11' => trans('cart.levering_op_adres'),
            'content2' => trans_choice('mail.order_success.content2', $order->type, [
                'first_name' => $order->isBuyYourSelf() ? ($contact->first_name ?? '?') : $user->first_name,
            ]),
            'content3' => trans('mail.order_success.content3', [
                'restaurant' => $workspace->name,
                'order_id' => $codeId,
                'date' => $dateLocal,
                'time' => $timeLocal,
                'type' => $order->type == \App\Helpers\OrderHelper::TYPE_DELIVERY
                    ? trans('cart.success_levering_confirm')
                    : trans('cart.success_afhalen_confirm'),
            ]),
            'content12' => trans('mail.order_success.content12', [
                'restaurant' => $workspace->name,
                'order_id' => $codeId,
                'date' => $dateLocal,
                'time' => $timeLocal,
            ]),
            'short_description' => trans('mail.order_success.short_description', [
                'restaurant' => $workspace->name,
                'order_id' => $codeId,
                'date' => $dateLocal,
                'time' => $timeLocal,
                'type' => $order->type_display,
            ]),
        ];
        
        //Is test account
        $dataContent['content_note'] = "";
        
        if ($order->is_test_account) {
            $dataContent['content2'] = trans('mail.reminder.content2', [
                'first_name' => strtoupper(trans('strings.admin')),
            ]);
            $dataContent['content_note'] = trans('mail.reminder.content_note');
        }
        
        if (!empty($toUser->email) || !empty($contact->email)) {
            SendEmailSuccessOrder::dispatch($toUser, $dataContent, 'layouts.emails.order-success', $lang);
        }
        
        app()->setLocale($currentLang);
        
        return true;
    }
    
    /**
     * Make paid order items
     *
     * @param  Order  $order
     * @return Order
     */
    public function makePaidOrderItems(Order $order)
    {
        \DB::table('order_items')
            ->where('order_id', $order->id)
            ->update([
                'paid' => true,
            ]);
        
        return $order;
    }
    
    /**
     * Validate timeslot detail when order
     *
     * @param  int  $settingTimeslotDetailId
     * @return bool
     */
    public function validateTimeslotDetail(int $settingTimeslotDetailId)
    {
        // Get timeslot detail
        /** @var \App\Models\SettingTimeslotDetail $settingTimeslotDetail */
        $settingTimeslotDetail = \App\Models\SettingTimeslotDetail::whereId($settingTimeslotDetailId)
            ->active()
            ->with(['settingTimeslot'])
            ->first();
        
        if (empty($settingTimeslotDetail)) {
            return false;
        }
        
        $settingTimeslot = $settingTimeslotDetail->settingTimeslot;
        
        // Max settings
        $maxOrder = (int) $settingTimeslotDetail->max;
        $maxPrice = (float) $settingTimeslot->max_price_per_slot;
        
        // Count from orders table
        $timeslotIds = [$settingTimeslotDetailId];
        $order = \App\Models\Order::whereIn('setting_timeslot_detail_id', $timeslotIds)
            ->where(function ($paymentInfo)
            {
                /** @var \Illuminate\Database\Eloquent\Builder $paymentInfo */
                $paymentInfo
                    // Payment method online with status is paid
                    ->where('status', \App\Models\Order::PAYMENT_STATUS_PAID)
                    // Or use payment method is cash / invoice
                    ->orWhereIn('payment_method',
                        [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]);
            })
            ->groupBy('setting_timeslot_detail_id')
            ->select('setting_timeslot_detail_id')
            ->addSelect(\DB::raw('(COUNT(id)) AS current_order'))
            ->addSelect(\DB::raw('(SUM(total_price)) AS current_price'))
            ->first();
        
        // When don't have any order with the timeslot id
        if (empty($order)) {
            return true;
        }
        
        // Get counters
        $currentOrder = (int) $order->current_order;
        $currentPrice = (float) $order->current_price;
        
        if ($currentOrder < $maxOrder && $currentPrice < $maxPrice) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Change the order status
     *
     * @param  Order  $order
     * @param  int  $status  Order status
     * @return Order
     */
    public function changeStatus(Order $order, int $status)
    {
        if (array_key_exists($status, $order->getPaymentStatuses())) {
            $order->status = $status;
            $order->save();
        }
        
        return $order;
    }
    
    public function applyGroupDiscount($orderItem, $discount)
    {
        $orderItem->total_price -= number_format($discount, 2);
        $orderItem->group_discount = $discount;
        
        return $orderItem;
    }
    
    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getOrderReferencesByWorkspaceAndLocalId($workspaceId, $localId)
    {
        return OrderReference::where('workspace_id', $workspaceId)
            ->where('local_id', $localId)
            ->get();
    }
    
    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getOrderReferencesByWorkspaceAndLocalIds($workspaceId, $localIds)
    {
        return OrderReference::where('workspace_id', $workspaceId)
            ->whereIn('local_id', $localIds)
            ->get();
    }
    
    /**
     * @param $provider
     * @param $localIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getOrderReferencesByWorkspaceAndProviderAndLocalIds($workspaceId, $provider, $localIds)
    {
        return OrderReference::where('workspace_id', $workspaceId)
            ->where('provider', $provider)
            ->whereIn('local_id', $localIds)
            ->get();
    }
    
    /**
     * Update or create new parent order
     *
     * @param  int  $workspaceId
     * @param  array  $attributes
     * @param  int  $type
     * @return Order
     */
    private function upsertParentOrder(int $workspaceId, array $attributes, int $type)
    {
        $parent = null;
        
        if ($type === \App\Helpers\OrderHelper::TYPE_IN_HOUSE) {
            $tableNumber = (int) $attributes['table_number'];
            $tableOrderLastPerson = Order::where('workspace_id', $workspaceId)
                ->where('table_number', $tableNumber)
                ->whereDate('date', array_get($attributes, 'date'))
                ->where('table_last_person', true)
                ->where(function($query) {
                    $query->whereHas('settingPayment', function ($query) {
                        $query->where('type', SettingPayment::TYPE_CASH);
                    })->orWhere(function ($query) {
                        $query->whereHas('settingPayment', function ($query) {
                            $query->where('type', SettingPayment::TYPE_MOLLIE);
                        })->where('status', Order::PAYMENT_STATUS_PAID);
                    });
                })
                ->orderBy('id', 'desc')
                ->first();
            
            if (empty($tableOrderLastPerson)) {
                $parent = Order::where('workspace_id', $workspaceId)
                    ->where('table_number', $tableNumber)
                    ->whereDate('date', array_get($attributes, 'date'))
                    ->whereNull('parent_id')
                    ->orderBy('id', 'asc')
                    ->first();
                
                if (empty($parent)) {
                    // Create parent order in the first time of the day
                    $parent = Order::create($attributes);
                }
            } else {
                $parent = Order::where('workspace_id', $workspaceId)
                    ->where('table_number', $tableNumber)
                    ->whereDate('date', array_get($attributes, 'date'))
                    ->where('id', '>', $tableOrderLastPerson->id)
                    ->whereNull('parent_id')
                    ->orderBy('id', 'asc')
                    ->first();
                
                if (empty($parent)) {
                    // Create parent order if previous order is completed  not exist
                    $parent = Order::create($attributes);
                }
            }
        }
        
        return $parent;
    }
    
    /**
     * Update or create new contact by email or phone
     *
     * @param  int  $workspaceId
     * @param  array  $data
     * @param  int  $type
     * @return Contact
     */
    public function upsertContact(int $workspaceId, array $data, int $type)
    {
        // Don't create new contact if email and phone is empty
        if (empty($data['email']) && empty($data['phone']) && empty($data['name'])) {
            return null;
        }
        
        $userId = (int) array_get($data, 'user_id');
        $user = User::findOrNew($userId);
        $workspace = Workspace::find($workspaceId);
        $wpMail = !empty($workspace) ? $workspace->email : 'contact@'.config('app.domain');
        $fakeEmail = false;

        if ($type === \App\Helpers\OrderHelper::TYPE_IN_HOUSE) {
            // Table ordering is not required name
            // We will fill name by phone number to prevent exception without name
            if (empty($data['name'])) {
                if (!empty($data['phone'])) {
                    $data['name'] = $data['phone'];
                } else {
                    if (!empty($data['email'])) {
                        $data['name'] = $data['email'];
                    } else {
                        $data['name'] = '-';
                    }
                }
            }
        } else {
            if ($type === \App\Helpers\OrderHelper::TYPE_SELF_ORDERING) {
                if (empty($data['email']) && empty($data['phone'])) {
                    $data['email'] = HelperFacade::generateFakeEmail($wpMail);
                    $fakeEmail = true;
                }
            }
        }
        
        // Update or create new contact by email or phone
        $contact = Contact::updateOrCreate([
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->gsm,
        ], [
            'locale' => $data['locale'] ?? $user->getLocale(),
            'name' => !empty($data['name']) ? $data['name'] : $user->name,
            'first_name' => !empty($data['name']) ? $data['name'] : $user->last_name,
            'last_name' => !empty($data['name']) ? '' : $user->last_name,
            'fake_email' => $fakeEmail
        ]);
        
        return $contact;
    }
    
    public function connectorOrderList($limit = 10, $workspaceId = null, $fromDateTime = null, $inOrderList = null, $dateOrdered = null, $dateOrderedFrom = null)
    {
        $orders = $this->makeModel()
            ->withCount(['childrenOrders AS children_orders_count' => function ($query) {
                $query->where('table_last_person', true);
            }])
            ->with(
                'workspace',
                'contact',
                'user',
                'orderItems',
                'orderItems.optionItems',
                'orderItems.optionItems.option',
                'orderItems.optionItems.optionItem',
                'childrenOrders',
                'group',
                'groupOrders',
                'groupOrders.user',
                'groupOrders.orderItems.optionItems',
                'groupOrders.orderItems.optionItems.option',
                'groupOrders.orderItems.optionItems.optionItem'
            )->where(function ($query)
            {
                $query->where(function ($subQuery)
                {
                    // individual
                    $subQuery->whereNull('group_id')->whereNotIn('type',
                        [Order::TYPE_IN_HOUSE, Order::TYPE_SELF_ORDERING]);
                })->orWhere(function ($subQuery)
                {
                    // in house & self ordering (parent order)
                    $subQuery->whereNull('group_id')->whereIn('type',
                        [Order::TYPE_IN_HOUSE, Order::TYPE_SELF_ORDERING])->whereNull('parent_id');
                })->orWhere(function ($subQuery)
                {
                    // group order
                    $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                });
            });
        
        if (!is_null($workspaceId)) {
            $orders = $orders->whereIn('workspace_id', Workspace::getPrinterGroupWorkspaceIds($workspaceId));
        }
        
        if (!is_null($fromDateTime)) {
            $orders = $orders->where(function($query) use ($fromDateTime) {
                $query->where('created_at', '>', $fromDateTime)
                ->orwhere('updated_at', '>', $fromDateTime)
                ->orWhereHas('groupOrders', function($subQuery) use ($fromDateTime) {
                    $subQuery->where('created_at', '>', $fromDateTime)
                        ->orWhere('updated_at', '>', $fromDateTime);
                });
            });
        }
        if(! is_null($inOrderList)) {
            $orders = OrderHelper::conditionShowOrderList($orders);
        }

        if (! is_null($dateOrdered)) {
            $orders = $orders->where(function($query) use ($dateOrdered) {
               $query->where('date', '=', $dateOrdered);
            });
        }

        if (! is_null($dateOrderedFrom)) {
            $orders = $orders->where(function($query) use ($dateOrderedFrom) {
                $query->where('date', '>=', $dateOrderedFrom);
            });
        }

        $orders = $orders->paginate($limit);

         return $orders->setCollection($orders->getCollection()->map(function ($item) {
            $item->payment_method_display = $item->payment_method_display;
            $item->payment_status_display = $item->payment_status_display;
            $item->status_display = $item->status_display;
            $item->items_count = $item->items_count;
            $item->group_discount = $item->group_discount;
            $item->table_last_person = true;
            $customerName = $item->user->name ?? '?';
            if ($item->type == \App\Helpers\OrderHelper::TYPE_IN_HOUSE) {
                $customerName = trans('order.label_table_number') . ' ' . ($item->table_number ?: '?');
                $item->table_last_person = $item->children_orders_count > 0;
            } elseif ($item->type === \App\Helpers\OrderHelper::TYPE_SELF_ORDERING) {
                $customerName = $item->contact->name ?? '?';
            } elseif (!empty($item->group_id)) {
                $customerName = $item->group->name ?? '?';
            }
            $item->customer_name = $customerName;

            return $item;
        }));
    }

    public function applyServiceCost(Order $order)
    {
        if(!empty($order) && !empty($order->workspace_id) && empty($order->group_id)) {
            $workspaceId = $order->workspace_id;
            $workspaceExtraServiceCost = WorkspaceExtra::where('type', WorkspaceExtra::SERVICE_COST)
                ->where('workspace_id', $workspaceId)
                ->where('active', true)
                ->first();

            if(!empty($workspaceExtraServiceCost)) {
                $settingServiceCost = SettingPreference::where('workspace_id', $workspaceId)->first();

                if(!empty($settingServiceCost) && !empty($settingServiceCost->service_cost_set)) {
                    $cost = 0;

                    if(!empty($settingServiceCost->service_cost_always_charge) || $order->subtotal < $settingServiceCost->service_cost_amount) {
                        $cost = $settingServiceCost->service_cost;
                    }

                    $order->service_cost = $cost;
                    $order->total_price += $cost;
                    $order->save();

                    return $order;
                }
            }
        }

        return $order;
    }

    public function adminGetListOrders($request, $perPage = 15)
    {
        $model = $this;
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        $workspaceId = $request->get('filter_workspace_id', null);
        if(!is_null($workspaceId)) {
            $model = $model->where('workspace_id', $workspaceId);
        }

        $filterTransformType = $request->get('filter_transform_type', null);
        if (!is_null($filterTransformType)) {
            $model = $model->where(function ($query) use ($filterTransformType) {
                $query->where('type', $filterTransformType)
                    ->orWhereHas('group', function ($subQuery) use ($filterTransformType) {
                        $subQuery->where('type', $filterTransformType);
                    });
            });

            if (in_array($filterTransformType, [\App\Helpers\OrderHelper::TYPE_IN_HOUSE])) {
                $model = $model->orderBy('status', 'asc')
                    ->orderBy('table_number', 'asc')
                    ->orderBy('created_at', 'desc');
            }
        }

        $filterPaymentMethod = $request->get('filter_payment_method', null);
        if (!is_null($filterPaymentMethod)) {
            $methodConvert = config('common.payment_method_convert.'.$filterPaymentMethod);
            $model = $model->where(function ($query) use ($methodConvert) {
                $query->whereIn('payment_method', $methodConvert)
                    ->orWhereHas('groupOrders', function ($subQuery) use ($methodConvert) {
                        $subQuery->whereIn('payment_method', $methodConvert);
                    });
            });
        }

        $filterPrinted = $request->get('filter_printed', null);
        if (!is_null($filterPrinted)) {
            if($filterPrinted == \App\Models\Order::PRINTED) {
                $model = $model->where('printed_werkbon', $filterPrinted);
            } else {
                $model = $model->where('printed_werkbon', 0);
                $now = now();
                $futureTenMinutes = $now->copy()->addMinutes(10);
                $passedTenMinutes = $now->copy()->subMinutes(10);

                if(
                    $filterPrinted == \App\Models\Order::NOT_PRINTED
                    || $filterPrinted == \App\Models\Order::NOT_PRINTED_AUTO_ENABLED
                ) {
                    $model = $model->where(function($query) use ($now, $passedTenMinutes, $futureTenMinutes) {
                        $query->where(function ($subQuery) use ($now, $futureTenMinutes) {
                            // Now < datetime. Before 10 minutes.
                            $subQuery->where('date_time', '>', $now)
                                ->where('date_time', '<=', $futureTenMinutes);
                        })->orWhere(function($subQuery)  use ($now, $passedTenMinutes) {
                            // Now >= datetime. Type: table & self ordering. It is more than 10 minutes in the future.
                            $subQuery->whereIn('type', \App\Helpers\OrderHelper::buyYourSelfTypes())
                                ->where('date_time', '<=', $now)
                                ->where('date_time', '<', $passedTenMinutes);
                        })->orWhere(function($subQuery)  use ($now) {
                            // Now >= datetime. Type: delivery, takeout.
                            $subQuery->whereNotIn('type', \App\Helpers\OrderHelper::buyYourSelfTypes())
                                ->where('date_time', '<=', $now);
                        });
                    });

                    if($filterPrinted == \App\Models\Order::NOT_PRINTED_AUTO_ENABLED) {
                        $model = $model->whereExists(function ($query) {
                            $query->selectRaw(1) // Selecting 1 is more efficient than selecting an ID
                                ->from('setting_prints')
                                ->whereColumn('setting_prints.workspace_id', 'orders.workspace_id')
                                ->whereNotNull('setting_prints.mac');
                        });
                    }
                } else {
                    $model = $model->where(function($query) use ($now, $passedTenMinutes, $futureTenMinutes) {
                        $query->where(function ($subQuery) use ($now, $futureTenMinutes) {
                            $subQuery->where('date_time', '>', $now)
                                ->where('date_time', '>', $futureTenMinutes);
                        })->orWhere(function($subQuery) use ($now, $passedTenMinutes) {
                            $subQuery->whereIn('type', \App\Helpers\OrderHelper::buyYourSelfTypes())
                                ->where('date_time', '<=', $now)
                                ->where('date_time', '>=', $passedTenMinutes);
                        });
                    });
                }
            }
        }

        $timezone = $request->get('timezone', 'UTC');
        $filterDate = $request->get('filter_datetime', null);
        if (!is_null($filterDate)) {
            $filterDateParts = explode('-', str_replace('/', '-', $filterDate));
            $filterDateString = $filterDateParts[2] . '-' . $filterDateParts[1] . '-' . $filterDateParts[0];

            $filterDateConvert = date('Y-m-d', strtotime($filterDateString));
            $model = $model->whereBetween('date_time', [
                Helper::convertDateTimeToUTC($filterDateConvert . ' 00:00:00', $timezone),
                Helper::convertDateTimeToUTC($filterDateConvert . ' 23:59:59', $timezone)
            ]);
        }

        $model = $model->whereNull('parent_id');

        if (!empty($request->sort_by) && $request->sort_by == 'workspace_id') {
            $model = $model->with(['workspace' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }

        $model = OrderHelper::conditionShowOrderList($model);
        $model = $model->paginate($perPage);

        $model->transform(function($order) {
            $order->print_class = $this->getPrintClass($order, $order->gereed);
            return OrderHelper::convertOrderItem($order);
        });

        return $model;
    }

    public function printItemByType($orderId, $order, $type) {
        $order = OrderHelper::sortOptionItems($order);
        $timezone = !empty($order->timezone) ? $order->timezone : 'UTC';
        $contents = OrderHelper::processPrint($order, $type, $timezone, true);

        if($type == config('print.all_type.a4')) {
            return view('manager.orders.prints.'.$type, $contents)->render();
        }

        $printItemProcess = OrderHelper::printItemProcess($type, $order, $contents);
        $contents = $printItemProcess['contents'];
        // @todo implement printContents to also show text based content
        $view = view('layouts.partials.print.content', compact('contents', 'type'))->render();
        OrderHelper::createJobAndCopyPrint($printItemProcess['data'], false);

        if($type == config('print.all_type.werkbon')) {
            $extraIds = OrderHelper::getCategoryIdsExtraWerkbon($orderId);

            if(!empty($extraIds)){
                $contents = OrderHelper::processPrint($order, $type, $timezone, true, $extraIds);
                $printItemProcess = OrderHelper::printItemProcess($type, $order, $contents);
                OrderHelper::createJobAndCopyPrint($printItemProcess['data'], false);
            }
        }

        $this->checkedAutoPrintWhenPrintManual([$orderId], $type);

        return $view;
    }

    public function checkedAutoPrintWhenPrintManual($orderIds, $type) {
        if(in_array($type, [
            'werkbon',
            'kassabon',
            'sticker'
        ])) {
            $orders = $this->makeModel()->whereIn('id', $orderIds)->get();

            if(!$orders->isEmpty()) {
                foreach ($orders as $order) {
                    $key = 'auto_print_'.$type;
                    $order->$key = true;
                    $order->save();
                }
            }
        }
    }
}
