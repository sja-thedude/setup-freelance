<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\Workspace;
use App\Repositories\StatisticRepository;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    private $statisticRepository;

    /**
     * DashboardController constructor.
     */
    public function __construct(StatisticRepository $statisticRepo)
    {
        parent::__construct();

        $this->statisticRepository = $statisticRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $timezone = 'UTC';
        $workspaceIds = null;
        $autoloadAjax = 1;
        $inOneDay = 0;

        if(empty($this->currentUser->is_super_admin)) {
            $workspaces = Workspace::where('account_manager_id', $this->currentUser->id)->get();

            if(!$workspaces->isEmpty()) {
                $workspaceIds = $workspaces->pluck('id')->all();
            }
        }

        if(!empty($rangeStartDate) && !empty($rangeEndDate)){
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);
            $startDate = date('Y-m-d', strtotime($rangeStartDate));
            $endDate = date('Y-m-d', strtotime($rangeEndDate));

            if(!empty($rangeStartDate)){
                $rangeStartDate = $startDate . ' ' . '00:00:00';
            }
            if(!empty($rangeEndDate)){
                $rangeEndDate = $endDate . ' ' . '23:59:59';
            }
            if($startDate == $endDate) {
                $inOneDay = 1;
            }
        } else {
            $rangeStartDate = $request->get('start_time', now());
            $rangeEndDate = $request->get('end_time', now());
        }

        $convertOrders = [];
        $totalOrderInTimes = 0;
        $totalRevenueInTimes = 0;
        $statisticInTimes = $this->statisticRepository->statisticOrderInTimes($rangeStartDate, $rangeEndDate, $timezone, $workspaceIds);
        $countSuccess = '(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NULL )+(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NOT NULL AND children.status = ' . Order::PAYMENT_STATUS_PAID . ' )';

        $convertOrders = $this->statisticRepository->convertStatisticOrders($statisticInTimes->get());
        $query = $statisticInTimes->where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->whereNull('group_id')->where('type', '!=', Order::TYPE_IN_HOUSE);
            })->orWhere(function ($subQuery) {
                $subQuery->whereNull('group_id')->where('type', Order::TYPE_IN_HOUSE)->whereNull('parent_id');
            })->orWhere(function ($subQuery) {
                $subQuery->whereNotNull('group_id')->whereNull('parent_id');
            });
        })
            ->addSelect([
                \DB::raw('(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id) AS children_orders_count'),
                \DB::raw('(SELECT COUNT(*) FROM orders AS children WHERE children.deleted_at is NULL AND children.parent_id = orders.id AND children.mollie_id IS NOT NULL AND children.status <> ' . Order::PAYMENT_STATUS_PAID . ') AS children_failed_count'),
                \DB::raw($countSuccess . ' AS children_success_count'),
                'orders.*'
            ])
            ->where(function ($query) use ($countSuccess) {
                $query->where(function ($query) use ($countSuccess) {
                    $query->where('type', Order::TYPE_IN_HOUSE)->whereRaw(\DB::raw($countSuccess . ' > 0'));
                })->orWhere('type', '!=', Order::TYPE_IN_HOUSE);
            })
            ->whereNull('deleted_at');
        $totalOrderInTimes = $query->count();
        $totalRevenueInTimes = 0;
        $orders = $this->statisticRepository->statisticManagerPerProduct(
            $rangeStartDate,
            $rangeEndDate,
            $timezone,
            null,
            $workspaceIds,
            false,
            [],
            []
        );

        $totalRevenueInTimes = collect($this->statisticRepository->groupByCategory($orders, false, false, []))
            ->map(function ($item) {return $item['cat_price'] ?? 0;})
            ->sum() + $orders->where('service_cost', '>', 0)->sum('service_cost') + $orders->where('ship_price', '>', 0)->sum('ship_price');
        $orderActives = $this->statisticRepository->statisticActiveOrders($workspaceIds)->count();
        $restaurantActives = $this->statisticRepository->statisticActiveRestaurants($workspaceIds)->count();
        $endUsers = $this->statisticRepository->statisticActiveEndUsers()->count();
        $viewData = compact(
            'convertOrders',
            'totalOrderInTimes',
            'totalRevenueInTimes',
            'orderActives',
            'restaurantActives',
            'endUsers',
            'autoloadAjax',
            'inOneDay'
        );

        if($request->ajax()) {
            $view = view($this->guard.'.dashboard.partials.order_charts', $viewData)->render();
            return $this->sendResponse(compact('view', 'convertOrders'), 'success');
        }

        return view($this->guard.'.dashboard.index', $viewData);
    }
}
