<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Country extends AppModel
{
    use Translatable;

    public $table = 'countries';

    public $timestamps = true;

    public $fillable = [
        'created_at',
        'updated_at',
        'active',
        'name',
        'code',
        'image'
    ];

    public $translationModel = 'App\Models\CountryTranslation';

    public $translationForeignKey = 'country_id';

    public $translatedAttributes = [
        'name'
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'active' => 'boolean',
        'name' => 'string',
        'code' => 'string',
        'image' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

    public $summary_fields = [
        'id',
        'name'
    ];

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public static function getCountriesList()
    {
        /** @var \App\Models\Country $instance */
        $instance = Country::getInstance();
        $datas = $instance->get();

        // Get list
        /** @var \Illuminate\Support\Collection $datas */
        if($datas->isNotEmpty()) {
            $datas = $datas->pluck('name', $instance->getKeyName())->all();
        }

        return $datas;
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\CountryTranslation::class);
    }
}
