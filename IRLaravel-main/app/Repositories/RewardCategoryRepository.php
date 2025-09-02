<?php

namespace App\Repositories;

use App\Models\RewardCategory;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class RewardCategoryRepository.
 *
 * @package namespace App\Repositories;
 */
class RewardCategoryRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RewardCategory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
