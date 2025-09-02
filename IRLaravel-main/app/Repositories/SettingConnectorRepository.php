<?php

namespace App\Repositories;

use App\Models\SettingConnector;

class SettingConnectorRepository extends AppBaseRepository
{
    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingConnector::class;
    }

    /**
     * @param $workspaceId
     * @param $perPage
     * @param $filters
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getLists($workspaceId, $perPage, $filters = null)
    {
        $query = $this->makeModel();

        $query = $query->select('setting_connectors.*')
            ->where('workspace_id', $workspaceId);

        if($perPage !== false) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
