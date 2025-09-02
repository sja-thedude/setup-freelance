<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'active',
        'name',
        'code',
        'image',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Country::class;
    }
}
