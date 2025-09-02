<?php

namespace App\Repositories;

use App\Models\PrinterGroup;
use Illuminate\Http\Request;

class PrinterGroupRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'active',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return PrinterGroup::class;
    }

    /**
     * Get list of group restaurant
     *
     * @param Request $request
     * @param $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getList(Request $request, $perPage)
    {
        $model = $this->makeModel();

        if (!empty($request->keyword_search)) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword_search. '%');
            });
        }

        $model->with([
            'printerGroupWorkspaces'
        ]);

        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        if (!empty($request->sort_by) && $request->sort_by == 'restaurants') {
            $model = $model->with(['printerGroupWorkspaces' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }

        $model = $model->paginate($perPage);

        return $model;
    }
}
