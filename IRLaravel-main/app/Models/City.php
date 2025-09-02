<?php

namespace App\Models;

class City extends AppModel
{
    public $table = 'cities';

    public $fillable = [
        'country_id',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'country_id' => 'required|integer',
        'name' => 'required|string|max:255'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(\App\Models\Address::class);
    }

    /**
     * @return array
     */
    public function getSummaryInfo()
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name
        ];
    }
}
