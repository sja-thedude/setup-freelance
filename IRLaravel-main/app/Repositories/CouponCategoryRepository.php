<?php

namespace App\Repositories;

use App\Models\CouponCategory;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CouponCategoryRepository.
 *
 * @package namespace App\Repositories;
 */
class CouponCategoryRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CouponCategory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
