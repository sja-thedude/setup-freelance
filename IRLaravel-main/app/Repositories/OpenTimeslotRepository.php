<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryOption;
use App\Models\OpenTimeslot;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OpenTimeslotRepository.
 *
 * @package namespace App\Repositories;
 */
class OpenTimeslotRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OpenTimeslot::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param       $conditionWhere
     * @param array $conditionWhereIn
     * @return mixed
     */
    public function deleteWhereIn($conditionWhere, $conditionWhereIn = [])
    {
        $queryBuilder = $this->model->where($conditionWhere);

        if ($conditionWhereIn) {
            $queryBuilder = $queryBuilder->whereIn($conditionWhereIn['column'], $conditionWhereIn['values']);
        }

        return $queryBuilder->delete();
    }
}
