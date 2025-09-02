<?php

namespace App\Models;

class PrinterGroup extends AppModel
{
    public $table = 'printer_groups';
    
    public $timestamps = true;

    public $fillable = [
        'active',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'name' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function printerGroupWorkspaces()
    {
        return $this->belongsToMany(
            \App\Models\Workspace::class,
            'printer_group_workspaces',
            'printer_group_id',
            'workspace_id'
        );
    }
}
