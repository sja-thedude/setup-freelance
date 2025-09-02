<?php

namespace App\Models;

class BannerTranslation extends AppModel
{
    public $timestamps = false;

    protected $fillable = [
        'title_1',
        'title_2',
        'button_1',
        'button_2',
        'link_1',
        'link_2',
        'description',
    ];

}
