<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class WorkspaceAppMeta extends AppModel
{
    use Translatable;

    public $table = 'workspace_app_meta';

    public $fillable = [
        'created_at',
        'updated_at',
        'active',
        'order',
        'workspace_app_id',
        'default',
        'key',
        'name',
        'type',
        'title',
        'description',
        'content',
        'icon',
        'url',
        'meta_data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
        'default' => 'boolean',
        'key' => 'string',
        'name' => 'string',
        'type' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'content' => 'string',
        'icon' => 'string',
        'url' => 'string',
        'meta_data' => 'array'
    ];

    public $translatedAttributes = [
        'name',
        'title',
        'description',
        'content',
        'url',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_app_id' => 'required',
        'name' => 'required|max:10',
        'title' => 'required|max:10',
        'description' => 'max:91',
        'url' => 'url',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspaceApp()
    {
        return $this->belongsTo(\App\Models\WorkspaceApp::class);
    }

    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'active' => $this->active,
            'order' => $this->order,
            'workspace_app_id' => $this->workspace_app_id,
            'default' => $this->default,
            'key' => $this->key,
            'name' => $this->name,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'icon' => $this->icon,
            'url' => $this->url,
            'meta_data' => $this->meta_data,
        ];
    }

}
