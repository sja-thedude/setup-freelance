<?php

namespace App\Repositories;

use App\Models\SettingPrint;

class SettingPrintRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'type',
        'mac',
        'copy',
        'auto',
        'type_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingPrint::class;
    }

    /**
     * @param $input
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreatePrint($input) {
        if(empty($input['print']) && empty($input['work_order']) && empty($input['sticker'])) {
            return false;
        }
        
        $ids = [];
        $workspaceId = $input['workspace_id'];
        
        foreach($input['print'] as $item) {
            $id = $item['id'];
            unset($item['id']);
            $item['workspace_id'] = $workspaceId;
            $item['auto'] = !empty($item['auto']) ? SettingPrint::VALUE_TRUE : SettingPrint::VALUE_FALSE;

            $print = $this->makeModel()->updateOrCreate([
                'workspace_id' => $input['workspace_id'],
                'id' => $id
            ], $item);
            
            $ids[] = $print->id;
            unset($item);
        }
        
        foreach($input['work_order'] as $item) {
            $id = $item['id'];
            unset($item['id']);
            $item['workspace_id'] = $workspaceId;
            $item['auto'] = !empty($item['auto']) ? SettingPrint::VALUE_TRUE : SettingPrint::VALUE_FALSE;

            $workOrder = $this->makeModel()->updateOrCreate([
                'workspace_id' => $input['workspace_id'],
                'id' => $id
            ], $item);
            
            $ids[] = $workOrder->id;
            unset($item);
        }
        
        if (!empty($input['sticker'])) {
            $sticker = $input['sticker'];
            $sticker['workspace_id'] = $workspaceId;
            $sticker['auto'] = !empty($sticker['auto']) ? SettingPrint::VALUE_TRUE : SettingPrint::VALUE_FALSE;
            
            $stickerPrint = $this->makeModel()->updateOrCreate([
                'workspace_id' => $workspaceId,
                'id' => $sticker['id']
            ], $sticker);
            
            $ids[] = $stickerPrint->id;
        }

        $this->makeModel()
            ->whereNotIn('id', $ids)
            ->where('workspace_id', $workspaceId)
            ->delete();

        return true;
    }

    /**
     * @param $workspaceId
     * @param $type
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getSettingPrintByType($workspaceId, $type) {
        return $this->makeModel()->where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->get();
    }

    /**
     * @param $workspaceId
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function initSettingPrintForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [
                [
                    'workspace_id' => $workspaceId,
                    'type' => 0,
                    'auto' => 0,
                    'type_id' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'workspace_id' => $workspaceId,
                    'type' => 1,
                    'auto' => 0,
                    'type_id' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'workspace_id' => $workspaceId,
                    'type' => 2,
                    'auto' => 0,
                    'type_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];

            if(!empty($data)) {
                SettingPrint::insert($data);
            }
        }

        return true;
    }
}
