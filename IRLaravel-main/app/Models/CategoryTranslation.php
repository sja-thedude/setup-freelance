<?php

namespace App\Models;

class CategoryTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'category_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'category_id',
        'locale',
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }
}
