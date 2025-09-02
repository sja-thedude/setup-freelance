<?php

namespace App\Repositories;

use App\Models\Option;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OptionRepository.
 *
 * @package namespace App\Repositories;
 */
class OptionRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Option::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * List all product
     *
     * @param Request $request
     * @param         $workspaceId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getAll(Request $request, $workspaceId)
    {
        $this->scopeQuery(function (Model $model) use ($request, $workspaceId) {

            $locale = app()->getLocale();

            $model = $model->select(['opties.*'])
                ->withCount('optionItems')
                ->with('optionItems')
                ->join('optie_translations', 'opties.id', '=', 'optie_translations.opties_id')
                ->where('workspace_id', $workspaceId)
                ->where('optie_translations.locale', $locale);

            //search by name
            if ($request->has('keyword_search') && $request->get('keyword_search') != '') {
                $name = $request->get('keyword_search');
                $model = $model->where('optie_translations.name', 'LIKE', "%$name%");
            }

            $model = $model->orderBy('order', 'ASC')->orderBy('created_at', 'ASC');

            return $model;
        });

        return $this->all();
    }

    public function updateOrderToPreventDuplicates($workspaceId) {
        DB::transaction(function () use ($workspaceId) {
            $order = 0;
            Option::where('workspace_id', (int) $workspaceId)
                ->orderBy('order')
                ->chunk(100, function ($options) use (&$order) {
                    foreach($options as $option) {
                        $order++;
                        $option->order = $order;
                        $option->save();
                    }
                });
        });
    }
}
