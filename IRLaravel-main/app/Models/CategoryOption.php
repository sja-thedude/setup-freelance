<?php

namespace App\Models;

class CategoryOption extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'category_opties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'category_id',
        'opties_id',
        'is_checked',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }
}
