<?php

namespace App\Repositories;

use App\Models\WorkspaceAppMeta;

class WorkspaceAppMetaRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'active',
        'order',
        'workspace_app_id',
        'default',
        'name',
        'title',
        'description',
        'content',
        'icon',
        'url',
        'meta_data'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return WorkspaceAppMeta::class;
    }

    /**
     * Change meta status
     *
     * @param WorkspaceAppMeta $workspaceAppMeta
     * @param bool $status
     * @return WorkspaceAppMeta
     */
    public function changeStatus(WorkspaceAppMeta $workspaceAppMeta, bool $status)
    {
        $workspaceAppMeta->active = $status;
        $workspaceAppMeta->save();

        return $workspaceAppMeta;
    }

    /**
     * Change order of settings
     *
     * @param array $orders
     * @return bool
     * @throws \Exception
     */
    public function changeOrders(array $orders)
    {
        \DB::beginTransaction();

        foreach ($orders as $id => $order) {
            $this->model
                ->where('id', $id)
                ->update([
                    'order' => $order,
                ]);
        }

        \DB::commit();

        return true;
    }

}
