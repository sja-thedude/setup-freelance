<?php

namespace App\Models;

class RestaurantCategory extends AppModel
{
    public $table = 'restaurant_categories';

    public $fillable = [
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
        'id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:255',
    ];

    public $summary_fields = [
        'id',
        'name',
    ];

    /**
     * @return array
     */
    public function getFullInfo() {
        return array_merge(parent::getFullInfo(), [
            'name' => $this->name,
        ]);
    }

    public static function getAll() {
        return static::all()->pluck('name', 'id')->toArray();
    }
}
