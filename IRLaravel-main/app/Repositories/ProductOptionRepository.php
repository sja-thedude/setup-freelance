<?php

namespace App\Repositories;

use App\Models\OptionItemReference;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductOptionRepository.
 *
 * @package namespace App\Repositories;
 */
class ProductOptionRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductOption::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
