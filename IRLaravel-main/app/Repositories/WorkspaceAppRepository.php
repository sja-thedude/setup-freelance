<?php

namespace App\Repositories;

use App\Models\WorkspaceApp;

class WorkspaceAppRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'active',
        'workspace_id',
        'theme'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return WorkspaceApp::class;
    }

    /**
     * @overwrite
     *
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        // Init default meta data
        if (!empty($model)) {
            $this->initDefaultWorkspaceAppMeta($model);
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        /** @var \App\Models\WorkspaceApp $model */
        $model = parent::updateOrCreate($attributes, $values);

        // Init default meta data
        if (!empty($model)) {
            $this->initDefaultWorkspaceAppMeta($model);
        }

        return $model;
    }

    /**
     * @param WorkspaceApp $workspaceApp
     * @return WorkspaceApp
     */
    public function initDefaultWorkspaceAppMeta(WorkspaceApp $workspaceApp)
    {
        $workspaceAppId = $workspaceApp->id;

        $data = [
            // Reserveren
            [
                /*'active' => false,
                'order' => 1,
                'workspace_app_id' => $workspaceAppId,
                'default' => true,*/
                'key' => 'reserve',
                'name' => 'Reserveren',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_1,
            ],
            // Reviews
            [
                'key' => 'reviews',
                'name' => 'Reviews',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_1,
            ],
            // Route
            [
                'key' => 'route',
                'name' => 'Route',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Jobs
            [
                'key' => 'jobs',
                'name' => 'Jobs',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_3,
            ],
            // Recent
            [
                'key' => 'recent',
                'name' => 'Recent',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Favorieten
            [
                'key' => 'favorites',
                'name' => 'Favorieten',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Account
            [
                'key' => 'account',
                'name' => 'Account',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Deel
            [
                'key' => 'share',
                'name' => 'Deel',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Klantkaart
            [
                'key' => 'loyalty',
                'name' => 'Klantkaart',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
            // Menukaart
            [
                'key' => 'menu',
                'name' => 'Menukaart',
                'type' => \App\Models\WorkspaceAppMeta::TYPE_2,
            ],
        ];

        foreach ($data as $k => $item) {
            $item = array_merge($item, [
                'active' => false,
                'order' => $k + 1,
                'workspace_app_id' => $workspaceAppId,
                'default' => true,
            ]);

            $workspaceApp->workspaceAppMeta()->firstOrCreate($item);
        }

        return $workspaceApp;
    }

    /**
     * Change theme of the App
     *
     * @param int $workspaceId Workspace ID by has one relation
     * @param int $themeId Theme ID. get from config/app_theme.php
     * @return int Number of records have been updated
     */
    public function changeTheme(int $workspaceId, int $themeId)
    {
        $updated = $this->model
            ->where('workspace_id', $workspaceId)
            ->update([
                'theme' => $themeId,
            ]);

        return $updated;
    }

}
