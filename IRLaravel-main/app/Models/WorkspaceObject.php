<?php

namespace App\Models;

class WorkspaceObject extends AppModel
{
    public $table = 'workspace_objects';

    public $fillable = [
        'active',
        'workspace_id',
        'model',
        'foreign_key',
        'meta_data',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'workspace_id' => 'integer',
        'model' => 'string',
        'foreign_key' => 'integer',
        'meta_data' => 'array',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * Get role of user in the workspace
     *
     * @param int $workspaceId
     * @param int $userId
     * @return \App\Models\Role
     */
    public function getRole(int $workspaceId, int $userId)
    {
        $role = null;
        /** @var \App\Models\WorkspaceObject $workspaceObject */
        $workspaceObject = \App\Models\WorkspaceObject::active()
            ->where('workspace_id', $workspaceId)
            ->where('model', \App\Models\User::class)
            ->where('foreign_key', $userId)
            ->first();

        if (!empty($workspaceObject) && !empty($workspaceObject->meta_data) && !empty($workspaceObject->meta_data['role_id'])) {
            $roleInstance = \App\Models\Role::getInstance();
            $role = \App\Models\Role::active()
                ->where($roleInstance->getKeyName(), (int)$workspaceObject->meta_data['role_id'])
                ->first();
        }

        if ($role == null) {
            // init empty object if null
            $role = new \App\Models\Role();
        }

        return $role;
    }

    /**
     * Attach object
     *
     * @param string $model
     * @param int $id
     * @param int $workspaceId
     * @param array|null $metaData
     * @return WorkspaceObject
     */
    public function attachObject(string $model, int $id, int $workspaceId, $metaData = null)
    {
        $workspaceObject = \App\Models\WorkspaceObject::where('workspace_id', $workspaceId)
            ->where('model', $model)
            ->where('foreign_key', $id)
            ->first();

        if (empty($workspaceObject)) {
            // Create new if not exist
            $workspaceObject = new \App\Models\WorkspaceObject([
                'active' => \App\Models\WorkspaceObject::IS_YES,
                'workspace_id' => $workspaceId,
                'model' => $model,
                'foreign_key' => $id,
                'meta_data' => $metaData,
            ]);

            $workspaceObject->save();
        }

        return $workspaceObject;
    }

    /**
     * Detach object
     *
     * @param string $model
     * @param int $id
     * @param int $workspaceId
     * @return bool
     * @throws \Exception
     */
    public function detachObject(string $model, int $id, int $workspaceId = 0)
    {
        /** @var \App\AppModel $workspaceObjects */
        $workspaceObjects = $this->where('model', $model)
            ->where('foreign_key', $id);

        // Delete by workspace id
        if (!empty($workspaceId)) {
            $workspaceObjects->where('workspace_id', $workspaceId);
        }

        return $workspaceObjects->delete();
    }

    /**
     * Attach object
     *
     * @param string $model
     * @param int $id
     * @param int $workspaceId
     * @param array|null $metaData
     * @return WorkspaceObject
     */
    public function reloadObject(string $model, int $id, int $workspaceId, $metaData = null)
    {
        // Cleanup
        $workspaceObjects = $this->where('model', $model)
            ->where('foreign_key', $id);

        // Delete by workspace id
        if (!empty($workspaceId)) {
            $workspaceObjects->where('workspace_id', $workspaceId);
        }

        $workspaceObjects->delete();

        // Create new
        $workspaceObject = new \App\Models\WorkspaceObject([
            'active' => \App\Models\WorkspaceObject::IS_YES,
            'workspace_id' => $workspaceId,
            'model' => $model,
            'foreign_key' => $id,
            'meta_data' => $metaData,
        ]);

        $workspaceObject->save();

        return $workspaceObject;
    }
}
