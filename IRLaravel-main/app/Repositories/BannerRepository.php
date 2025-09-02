<?php

namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'photo',
        'title_1',
        'title_2',
        'button_1',
        'button_2',
        'link_1',
        'link_2',
        'align',
        'order',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Banner::class;
    }
}
