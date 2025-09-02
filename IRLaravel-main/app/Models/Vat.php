<?php

namespace App\Models;

use App\Facades\Helper;

class Vat extends AppModel
{
    public $table = 'vats';

    public $fillable = [
        'key',
        'name',
        'in_house',
        'take_out',
        'delivery',
        'country_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'key' => 'string',
        'name' => 'string',
        'in_house' => 'float',
        'take_out' => 'float',
        'delivery' => 'float',
        'country_id' => 'integer',
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
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     * Mutator for fullname attribute
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        $name = $this->name;

        if (!empty($this->key)) {
            $name = trans('vat.default_items.' . $this->key . '.name');
        }

        return vsprintf('%s (%s%% / %s%% / %s%%)', [
            $name,
            $this->take_out,
            $this->delivery,
            $this->in_house,
        ]);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'key' => $this->key,
            'name' => $this->name,
            'in_house' => $this->in_house,
            'take_out' => $this->take_out,
            'delivery' => $this->delivery,
            'country_id' => $this->country_id,
            'country' => (!empty($this->country_id) && !empty($this->country)) ? $this->country->getSummaryInfo() : null,
        ];
    }

    /**
     * @param $countryId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getVats($countryId)
    {
        $vats = static::where('country_id', $countryId)->get();
        $uniques = [];

        if (!$vats->isEmpty()) {
            foreach ($vats as $key => $vat) {
                $data = [
                    $vat->take_out,
                    $vat->delivery,
                    $vat->in_house,
                ];

                $uniques = array_unique(array_merge($uniques, $data));
            }
        }

        asort($uniques);
        return $uniques;
    }
}
