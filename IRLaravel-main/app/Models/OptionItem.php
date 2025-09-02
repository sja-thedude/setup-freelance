<?php

namespace App\Models;

use App\Facades\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionItem extends AppModel
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'optie_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'opties_id',
        'name',
        'price',
        'currency',
        'available',
        'master',
        'order',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    public $casts = [
        'opties_id' => 'integer',
        'name' => 'string',
        'price' => 'float',
        'currency' => 'string',
        'available' => 'boolean',
        'master' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartOptionItems()
    {
        return $this->hasMany(\App\Models\CartOptionItem::class, 'optie_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'deleted_at' => $this->deleted_at,
            'name' => $this->name,
            'price' => Helper::formatCurrencyNumber($this->price),
            'currency' => $this->currency,
            'available' => $this->available,
            'master' => $this->master,
            'order' => (!empty($this->order)) ? $this->order : 0,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function optionItemReferences()
    {
        return $this->hasMany(\App\Models\OptionItemReference::class, 'local_id');
    }
}
