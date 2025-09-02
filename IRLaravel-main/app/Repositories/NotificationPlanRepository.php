<?php

namespace App\Repositories;

use App\Models\NotificationPlan;
use Illuminate\Http\Request;

class NotificationPlanRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'workspace_id',
        'platform',
        'title',
        'description',
        'is_send_everyone',
        'location',
        'location_lat',
        'location_long',
        'location_radius',
        'send_now',
        'send_datetime',
        'gender_dest_male',
        'gender_dest_female',
        'start_age_dest',
        'end_age_dest',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return NotificationPlan::class;
    }

    /**
     * @param Request $request
     * @param $platform
     * @param int $perPage
     * @param null $workspaceId
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getLists(Request $request, $platform, $perPage = 10, $workspaceId = null)
    {
        $model = $this->makeModel()
            ->with(['workspace', 'notificationCategories'])
            ->where('platform', $platform);
        
        if (!empty($workspaceId)) {
            $model = $model->where('workspace_id', $workspaceId);
        }

        // Search by name
        if (!empty($request->keyword)) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';
        
        $model = $model->orderBy($sortBy, $orderBy);
        
        $model = $model->paginate($perPage);

        return $model;
    }
}
