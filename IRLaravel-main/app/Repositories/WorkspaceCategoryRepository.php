<?php

namespace App\Repositories;

use App\Models\WorkspaceCategory;

class WorkspaceCategoryRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'restaurant_category_id'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return WorkspaceCategory::class;
    }
}
