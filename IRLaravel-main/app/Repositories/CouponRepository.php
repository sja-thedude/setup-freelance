<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\Order;
use Carbon\Carbon;

class CouponRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'active',
        'workspace_id',
        'code',
        'max_time_all',
        'max_time_single',
        'currency',
        'discount',
        'expire_time'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Coupon::class;
    }

    /**
     * @overwrite
     * @param null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $locale = \App::getLocale();
        $couponUsedFromOrderStatus = Order::getCouponUsedFromOrderStatus();
        $couponUsedFromOrderStatusStr = implode(',', $couponUsedFromOrderStatus);

        // Filter
        $this->scopeQuery(function ($model) use ($request, $locale, $couponUsedFromOrderStatusStr) {
            /** @var \Illuminate\Database\Eloquent\Builder $model */

            /** @var \App\Models\Coupon $assocModel */
            $assocModel = $model->getModel();
            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

            // Prevent duplicate field
            $model = $model->select('coupons.*')
                // with relations
                ->with(['workspace'])
                // Join with related tables
                ->join('coupon_translations', 'coupon_translations.coupon_id', '=', 'coupons.id')
                ->where('coupon_translations.locale', $locale);

            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->where('coupons.workspace_id', $workspaceId);
            }

            // Filter by active status
            if ($request->has('active') && $request->get('active') != '') {
                $isActive = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
                $model = $model->where('coupons.active', $isActive);
            }

            if ($request->has('is_expire_time') && $request->get('is_expire_time') != '') {
                $model = $model->where('coupons.expire_time', ">=", Carbon::now()->format("Y-m-d H:i"));
            }

            // Search by keyword: name, description
            if ($request->has('keyword') && trim($request->get('keyword') . '') != '') {
                $keyword = $request->get('keyword');

                $model = $model->where(function ($query) use ($keyword) {
                        $query->where('coupons.code', 'LIKE', "%{$keyword}%")
                            ->orWhere('coupon_translations.promo_name', 'LIKE', "%{$keyword}%");
                    });
            }

            // Only show when expire_time > now
            if (!empty($request->get('hide_expire_time'))) {
                $model = $model->where('coupons.expire_time', '>', \Carbon\Carbon::now());
            }

            // Only show when max_time_all > total used in orders
            if (!empty($request->get('hide_max_time_all'))) {
                $model = $model->whereRaw(\DB::raw("(SELECT COUNT(orders.coupon_id) 
                    FROM orders 
                    WHERE orders.coupon_id = coupons.id 
                        AND orders.status IN ({$couponUsedFromOrderStatusStr})
                ) < coupons.max_time_all"));
            }

            if (!empty($request->get('count_orders'))) {
                // Reject master order group
                $model->addSelect(\DB::raw("(SELECT COUNT(orders.id) 
                    FROM orders 
                    WHERE orders.coupon_id = coupons.id 
                        AND orders.status IN ({$couponUsedFromOrderStatusStr})
                        AND (orders.group_id IS NULL OR orders.parent_id IS NOT NULL)
                ) AS count_orders"));
            }

            // Order by from request
            if (!empty($orderBy)) {
                if ($assocModel->isTranslationAttribute($orderBy)) {
                    // Order by in translation table
                    $model = $model->orderBy('coupon_translations.' . $orderBy, $sortBy);
                } else {
                    // Order by main table
                    $model = $model->orderBy($assocModel->getTable() . '.' . $orderBy, $sortBy);
                }
            } else {
                // Default order by
                $model = $model->orderBy($assocModel->getTable() . '.created_at', 'desc');
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * Validate coupon by the code
     *
     * @param string $code Coupon code
     * @return \App\Models\Coupon
     */
    public function validateCode($workspaceId, string $code)
    {
        return $this->model
            ->where('workspace_id', $workspaceId)
            ->where('code', 'LIKE', $code)
            //->where('active', true) // Active = invisible in view
            ->first();
    }

}
