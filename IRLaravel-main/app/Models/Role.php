<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends AppModel
{
    use SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_MANAGER = 2;
    const ROLE_USER = 3;

    const PLATFORM_BACKOFFICE = 0; // Admin, Backend
    const PLATFORM_MANAGER = 1; // Manager
    const PLATFORM_FRONTEND = 2; // Frontend

    protected $dates = ['deleted_at'];

    public $table = 'roles';

    public $fillable = [
        'platform',
        'active',
        'name',
        'description',
        'permission',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'platform' => 'boolean',
        'active' => 'boolean',
        'name' => 'string',
        'description' => 'string',
        'permission' => 'array'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    /**
     * @param bool $overwrite
     * @return bool
     */
    public function cachePermission($overwrite = true)
    {
        $key = config('cache.key').$this->id;

        // Ignore if not allow overwrite with exist file
        if (cache()->has($key) && !$overwrite) {
            return true;
        }

        $varContent = $this->toArray();

        return cache()->forever($key, $varContent);
    }

    /**
     * @return bool
     */
    public function cleanCachePermission()
    {
        $key = config('cache.key').$this->id;

        if (!cache()->has($key)) {
            return true;
        }

        return cache()->forget($key);
    }

    /**
     * Fire events when create, update roles
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // When saved
        static::saved(function ($model) {
            $model->cachePermission();
        });

        static::deleted(function ($model) {
            $model->cleanCachePermission();
        });
    }

    /**
     * Get init user role:
     * 1: Administrator
     * 2: User
     *
     * @return array
     */
    public function getHiddenRoles()
    {
        return [static::ROLE_USER];
    }

    /**
     * Get normal user roles:
     * 2: User
     *
     * @return array
     */
    public function getNormalUserRoles()
    {
        return [static::ROLE_USER];
    }

    /**
     * Get Admin user role
     *
     * @return array
     */
    public function getAdminRoles()
    {
        return [static::ROLE_ADMIN];
    }

    /**
     * Scope a query to only include platform roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope a query to join with workspace_objects
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param int $workspaceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithWorkspace(\Illuminate\Database\Eloquent\Builder $model, int $workspaceId)
    {
        $thisInstance = $this;
        $model->select($thisInstance->getTable() . '.*')->rightJoin('workspace_objects', function ($join) use ($thisInstance, $workspaceId) {
            // Right join with workspace_objects table
            $join->where(function ($query) use ($thisInstance, $workspaceId) {
                $query->where('workspace_objects.foreign_key', \DB::raw($thisInstance->getTable() . '.' . $thisInstance->getKeyName()));
                $query->where('workspace_objects.workspace_id', $workspaceId);
                $query->where('workspace_objects.active', \App\Models\WorkspaceObject::IS_YES);
                $query->where('workspace_objects.model', static::class);
            });
            // Default include Administrator role
            $join->orWhere(function ($query) use ($thisInstance) {
                $query->orWhereIn($thisInstance->getTable() . '.' . $thisInstance->getKeyName(), $thisInstance->getAdminRoles());
            });
        });

        // Prevent duplicate
        $model->where('platform', self::PLATFORM_BACKOFFICE)->groupBy($thisInstance->getTable() . '.' . $thisInstance->getKeyName());

        return $model;
    }
}
