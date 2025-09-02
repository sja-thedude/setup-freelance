<?php

namespace App\Repositories;

use App\Models\SettingGeneral;

class SettingGeneralRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'title',
        'subtitle',
        'primary_color',
        'second_color',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingGeneral::class;
    }
    
    public function initSettingGeneralForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [
                'workspace_id' => $workspaceId,
                'primary_color' => SettingGeneral::PRIMARY_COLOR,
                'second_color' => SettingGeneral::SECOND_COLOR,
                'created_at' => $now,
                'updated_at' => $now
            ];

            if(!empty($data)) {
                SettingGeneral::insert($data);
            }
        }

        return true;
    }
}
