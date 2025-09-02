<?php

namespace App\Models;

class Address extends AppModel
{
    public $table = 'addresses';

    public $fillable = [
        'city_id',
        'postcode',
        'address',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'postcode' => 'string',
        'address' => 'string',
        'latitude' => 'string',
        'longitude' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'city_id' => 'required|integer',
        'postcode' => 'required|string|max:12',
        'address' => 'required|string|max:255'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules_location = [
        'latitude' => 'required|string|max:20',
        'longitude' => 'required|string|max:20',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function getSummaryInfo()
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'postcode' => $this->postcode,
            'address' => $this->address,
        ];
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return array_merge($this->getSummaryInfo(), [
            'city' => $this->city->getSummaryInfo(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }
}
