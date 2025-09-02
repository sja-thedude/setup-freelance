<?php

namespace App\Models;

class SettingPrint extends AppModel
{
    const TYPE_KASSABON = 0;
    const TYPE_WERKBON = 1;
    const TYPE_STICKER = 2;
    
    const VALUE_FALSE = 0; 
    const VALUE_TRUE = 1;
    
    const TIMESLOT = 0;
    const IDENTICAL_PRODUCTS = 1;

    public $table = 'setting_prints';
    
    public $timestamps = true;

    public $fillable = [
        'workspace_id',
        'type',
        'mac',
        'copy',
        'auto',
        'type_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'integer',
        'mac' => 'string',
        'copy' => 'integer',
        'auto' => 'boolean',
        'type_id' => 'integer'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function printerJobs()
    {
        return $this->hasMany(\App\Models\PrinterJob::class, 'printer_id');
    }

    /**
     * @param null $value
     * @return array|string
     */
    public static function getTypes($value = null)
    {
        $options = array(
            static::TIMESLOT => trans('setting.more.timeslot'),
            static::IDENTICAL_PRODUCTS => trans('setting.more.identical_products')
        );
        return static::enum($value, $options);
    }
}
