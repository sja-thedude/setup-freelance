<?php

namespace App\Repositories;

use App\Models\ProductAllergenen;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductAllergenenRepository.
 *
 * @package namespace App\Repositories;
 */
class ProductAllergenenRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductAllergenen::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
