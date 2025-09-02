<?php

namespace App\Models;

class PrinterGroupWorkspace extends AppModel
{
    public $table = 'printer_group_workspaces';
    
    public $timestamps = true;

    public $fillable = [
        'printer_group_id',
        'workspace_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
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
    public function printerGroup()
    {
        return $this->belongsTo(\App\Models\PrinterGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }
}
