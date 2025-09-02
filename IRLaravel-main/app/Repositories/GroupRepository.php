<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\Order;

class GroupRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'workspace_id',
        'name',
        'company_name',
        'company_street',
        'company_number',
        'company_vat_number',
        'company_city',
        'company_postcode',
        'payment_mollie',
        'payment_payconiq',
        'payment_cash',
        'payment_factuur',
        'close_time',
        'receive_time',
        'type',
        'contact_email',
        'contact_name',
        'contact_surname',
        'contact_gsm'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Group::class;
    }

    /**
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate", $with = ['workspace', 'orders'])
    {
        $request = request();
        

        // Filter
        $this->scopeQuery(function ($model) use ($request, $with) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            // Prevent duplicate field
                $model = $model->select('groups.*')
                    ->with($with);
            

            if(!$request->has('managerWeb') || !$request->managerWeb) {
                $model = $model->where('active', 1);
            }

            //Sort
            if ($request->has('sort_by')) {
                $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
                $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';
                $model = $model->orderBy($sortBy, $orderBy);
            }

            // Search by keyword
            if ($request->has('keyword')) {
                $keyword = $request->get('keyword');
                $model = $model->where('groups.name', 'LIKE', "%{$keyword}%");
            }

            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->where('workspace_id', $workspaceId);
            }

            $model = $model->orderBy('groups.name', 'asc');

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    public function updateOrderWhenChangeTime($group) {
        if(!$group->orders->isEmpty()) {
            $orderParentNeedUpdates = $group->orders()
                ->whereNull('parent_id')
                ->where('auto_print_sticker', false)
                ->where('auto_print_werkbon', false)
                ->where('auto_print_kassabon', false)
                ->get();

            if(!$orderParentNeedUpdates->isEmpty()) {
                $parentIds = $orderParentNeedUpdates->pluck('id')->all();
                $allOrderUpdate = $group->orders()
                    ->where(function($query) use ($parentIds) {
                        $query->whereIn('id', $parentIds)
                            ->orWhereIn('parent_id', $parentIds);
                    })
                    ->get();

                foreach ($allOrderUpdate as $order) {
                    $localDateTime = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone);
                    $newLocalDateTime = implode(' ', [
                        date('Y-m-d', strtotime($localDateTime)),
                        $group->receive_time
                    ]);
                    $newUtcDateTime = \App\Helpers\Helper::convertDateTimeToUTC($newLocalDateTime, $order->timezone);
                    $newUtcDate = date('Y-m-d', strtotime($newUtcDateTime));
                    $newUtcTime = date('H:i:s', strtotime($newUtcDateTime));
                    $order->date_time = $newUtcDateTime;
                    $order->date = $newUtcDate;
                    $order->time = $newUtcTime;
                    $order->save();
                }
            }
        }
    }
}
