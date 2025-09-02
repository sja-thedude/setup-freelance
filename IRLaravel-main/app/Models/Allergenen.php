<?php

namespace App\Models;

use App\Facades\Helper;

class Allergenen extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'allergenens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'icon',
        'type',
    ];

    const TYPE_EI = 1;
    const TYPE_GLUTEN = 2;
    const TYPE_LUPINE = 3;
    const TYPE_MELK = 4;
    const TYPE_MOSTERD = 5;
    const TYPE_NOTEN = 6;
    const TYPE_PINDAS = 7;
    const TYPE_SCHAALD = 8;
    const TYPE_SELDERIJ = 9;
    const TYPE_SESAMZAAD = 10;
    const TYPE_SOJA = 11;
    const TYPE_VIS = 12;
    const TYPE_WEEKDIEREN = 13;
    const TYPE_ZWAVEL = 14;

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_EI => trans('allergenen.types.ei'),
            static::TYPE_GLUTEN => trans('allergenen.types.gluten'),
            static::TYPE_LUPINE => trans('allergenen.types.lupine'),
            static::TYPE_MELK => trans('allergenen.types.melk'),
            static::TYPE_MOSTERD => trans('allergenen.types.mosterd'),
            static::TYPE_NOTEN => trans('allergenen.types.noten'),
            static::TYPE_PINDAS => trans('allergenen.types.pindas'),
            static::TYPE_SCHAALD => trans('allergenen.types.schaald'),
            static::TYPE_SELDERIJ => trans('allergenen.types.selderij'),
            static::TYPE_SESAMZAAD => trans('allergenen.types.sesamzaad'),
            static::TYPE_SOJA => trans('allergenen.types.soja'),
            static::TYPE_VIS => trans('allergenen.types.vis'),
            static::TYPE_WEEKDIEREN => trans('allergenen.types.weekdieren'),
            static::TYPE_ZWAVEL => trans('allergenen.types.zwavel'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productAllergenens()
    {
        return $this->hasMany(\App\Models\ProductAllergenen::class, 'allergenen_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'product_allergenens');
    }

    /**
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        if (empty($this->type)) {
            return '';
        }

        $allTypes = $this->getTypes();

        return array_get($allTypes, $this->type, $this->type);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        $icon = '';

        if (!empty($this->icon)) {
            // Change gray to color icons
            $icon = str_replace('/gray/', '/hover/', $this->icon);
            // Trim source character
            $icon = trim($icon, '/');
        }

        return [
            'id' => $this->getKey(),
            'icon' => (!empty($icon)) ? Helper::getLinkFromDataSource($icon) : null,
            'type' => $this->type,
            'type_display' => $this->type_display,
        ];
    }

}
