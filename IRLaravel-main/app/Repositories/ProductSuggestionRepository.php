<?php

namespace App\Repositories;

use App\Models\ProductOption;
use App\Models\ProductSuggestion;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductSuggestionRepository.
 *
 * @package namespace App\Repositories;
 */
class ProductSuggestionRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductSuggestion::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
