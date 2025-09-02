<?php

namespace App\Repositories;

use App\Models\SettingDeliveryConditions;

class SettingDeliveryConditionsRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'area_start',
        'area_end',
        'price_min',
        'price',
        'free',
        'workspace_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingDeliveryConditions::class;
    }

    /**
     * @param $input
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreateDelivery($input) {
        if(empty($input['delivery'])) {
            return false;
        }
        
        $ids = [];
        $workspaceId = $input['workspace_id'];
        
        foreach($input['delivery'] as $item) {
            $id = $item['id'];
            unset($item['id']);
            $item['workspace_id'] = $workspaceId;

            $delivery = $this->makeModel()->updateOrCreate([
                'workspace_id' => $input['workspace_id'],
                'id' => $id
            ], $item);
            
            $ids[] = $delivery->id;
        }
        
        $this->makeModel()
            ->whereNotIn('id', $ids)
            ->where('workspace_id', $workspaceId)
            ->delete();
        
        return true;
    }

    /**
     * @param $workspaceId
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function initSettingDeliveryConditionsForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [
                'workspace_id' => $workspaceId,
                'area_start' => config('settings.delivery.area_start'),
                'area_end' => config('settings.delivery.area_end'),
                'price_min' => config('settings.delivery.price_min'),
                'price' => config('settings.delivery.price'),
                'free' => config('settings.delivery.free'),
                'created_at' => $now,
                'updated_at' => $now
            ];

            if(!empty($data)) {
                SettingDeliveryConditions::insert($data);
            }
        }

        return true;
    }
}
