<?php

namespace App\Repositories;

use App\Models\CountryTranslation;

class CountryTranslationRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'country_id',
        'locale',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return CountryTranslation::class;
    }
}
