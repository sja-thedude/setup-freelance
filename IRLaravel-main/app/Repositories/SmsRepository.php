<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Sms;
use App\Models\PrinterGroupWorkspace;

class SmsRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'status',
        'message',
        'sent_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Sms::class;
    }


    public function getList($request) {
        $workspaceId = $request->get('workspace_id', null);
        $startDate = $request->get('start_date', null);
        $endDate = $request->get('end_date', null);
        $model = $this->makeModel();
        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        if(!is_null($workspaceId)) {
            $model = $model->where('workspace_id', $workspaceId);
        }

        if(!is_null($startDate)) {
            $model = $model->where('sent_at', '>=', $startDate . ' 00:00:00');
        } else {
            $model = $model->where('sent_at', '>=', date('Y-m-d') . ' 00:00:00');
        }

        if(!is_null($endDate)) {
            $model = $model->where('sent_at', '<=', $endDate . ' 23:59:59');
        } else {
            $model = $model->where('sent_at', '<=', date('Y-m-d') . ' 23:59:59');
        }

        if (!empty($request->sort_by) && $request->sort_by == 'workspace_id') {
            $model = $model->with(['workspace' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }

        return $model->paginate();
    }
}
