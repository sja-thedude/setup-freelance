<?php

namespace App\Repositories;

use App\Models\RestaurantCategory;
use App\Models\User;
use Illuminate\Http\Request;

class RestaurantCategoryRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return RestaurantCategory::class;
    }

    /**
     * @overwrite
     *
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();

        // Filter
        $this->scopeQuery(function ($model) use ($request, $arrRequest) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            // Prevent duplicate field
            $model = $model->select('restaurant_categories.*');

            // Filter by location: lat, long, radius
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->join('workspace_category',
                    'workspace_category.restaurant_category_id', '=', 'restaurant_categories.id')
                    ->where('workspace_category.workspace_id', $workspaceId)
                    ->orderBy('restaurant_categories.name', 'asc')
                    ->groupBy('restaurant_categories.id');
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * @param Request $request
     * @param int $perPage
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getLists(Request $request, $perPage = 10)
    {
        $model = $this->makeModel();

        // Search by name
        if (!empty($request->keyword)) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword. '%');
            });
        }

        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';
        
        $model = $model->orderBy($sortBy, $orderBy);
        
        $model = $model->paginate($perPage);

        return $model;
    }
}
