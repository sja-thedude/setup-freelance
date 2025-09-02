<?php

namespace App\Models;

class WorkspaceApp extends AppModel
{
    public $table = 'workspace_apps';

    public $fillable = [
        'created_at',
        'updated_at',
        'active',
        'workspace_id',
        'theme'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'theme' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workspaceAppMeta()
    {
        return $this->hasMany(\App\Models\WorkspaceAppMeta::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(\App\Models\WorkspaceAppMeta::class);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'theme' => $this->theme,
            'meta' => $this->meta->transform(function ($meta) {
                /** @var \App\Models\WorkspaceAppMeta $meta */
                return $meta->getFullInfo();
            }),
        ];
    }

}
