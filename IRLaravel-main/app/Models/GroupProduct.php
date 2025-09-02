<?php

namespace App\Models;

class GroupProduct extends AppModel
{
    protected $table = 'group_products';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'product_id',
        'group_id'
    ];
}