<?php

namespace App\Repositories;

use App\Models\CouponCategory;
use App\Models\CouponProduct;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CouponProductRepository.
 *
 * @package namespace App\Repositories;
 */
class CouponProductRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CouponProduct::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
