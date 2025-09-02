<?php

namespace App\Models;

class ProductTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'product_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'product_id',
        'locale',
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }
}
