<?php

namespace App\Models;

class CategoryRelation extends AppModel
{
    public $table = 'categories_relation';

    public $timestamps = true;

    public $fillable = [
        'foreign_model',
        'foreign_id',
        'category_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}
