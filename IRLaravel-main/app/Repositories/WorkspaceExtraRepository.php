<?php

namespace App\Repositories;

use App\Models\WorkspaceExtra;

class WorkspaceExtraRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'active',
        'type',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return WorkspaceExtra::class;
    }

    /**
     * @overwrite
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        /** @var \App\Models\WorkspaceExtra $model */
        $model = parent::updateOrCreate($attributes, $values);

        // Update active status for Manage App of Workspace
        if ($model->type == WorkspaceExtra::OWN_MOBILE_APP) {
            $workspaceAppRepo = new WorkspaceAppRepository(app());
            $workspaceAppRepo->updateOrCreate([
                'workspace_id' => $model->workspace_id,
            ], [
                'workspace_id' => $model->workspace_id,
                'active' => $model->active,
            ]);
        }

        return $model;
    }

}
