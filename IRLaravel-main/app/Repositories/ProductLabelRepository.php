<?php

namespace App\Repositories;

use App\Models\ProductLabel;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductLabelRepository.
 *
 * @package namespace App\Repositories;
 */
class ProductLabelRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductLabel::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
