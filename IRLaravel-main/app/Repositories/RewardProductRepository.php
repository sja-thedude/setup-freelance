<?php

namespace App\Repositories;

use App\Models\RewardProduct;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class RewardProductRepository.
 *
 * @package namespace App\Repositories;
 */
class RewardProductRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RewardProduct::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
