<?php

namespace App\Repositories;

use App\Helpers\OrderHelper;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CategoryRepository.
 *
 * @package namespace App\Repositories;
 */
class CategoryRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'individual',
        'group',
        'available_delivery',
        'available_in_house',
        'exclusively_in_house',
        'favoriet_friet',
        'kokette_kroket',
        'time_no_limit',
        'active',
        'order',
        'created_at',
        'updated_at'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }

    /**
     * @overwrite
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();
        $timezone = $request->header('Timezone');
        $clientTimestamp = \Carbon\Carbon::now($timezone);
        $type = $request->get('type', '');
        $locale = \App::getLocale();

        $this->scopeQuery(function ($model) use ($request, $arrRequest, $clientTimestamp, $locale, $type) {
            /** @var \Illuminate\Database\Eloquent\Builder $model */

            /** @var \App\Models\Coupon $assocModel */
            $assocModel = $model->getModel();
            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

            // Prevent duplicate field
            $model = $model->select('categories.*')
                ->groupBy('categories.id');

            if(!empty($type) && in_array($type, [OrderHelper::TYPE_IN_HOUSE, OrderHelper::TYPE_SELF_ORDERING])) {
                switch ($type) {
                    case OrderHelper::TYPE_IN_HOUSE:
                        $model = $model->where(function($query) {
                            $query->orWhere('categories.available_in_house', true)
                                ->orWhere('categories.exclusively_in_house', true);
                        });
                        break;
                    case OrderHelper::TYPE_SELF_ORDERING:
                        $model = $model->where('categories.exclusively_in_house', false);
                        break;
                    default:
                        break;
                }
            } else {
                $model = $model->where([
                    'categories.exclusively_in_house' => false
                ]);
            }

            // With related tables
            $with = ['translations', 'categoryAvatar', 'workspace'];

            if ($request->has('with')) {
                $qWith = $request->get('with');

                // Convert to array
                if (!is_array($qWith)) {
                    $qWith = [$qWith];
                }

                if (in_array('products', $qWith)) {
                    $with = array_merge($with, [
                        'products' => function ($query) use ($request, $arrRequest) {
                            // Filter by active status
                            if (array_key_exists('products.active', $arrRequest)) {
                                $isActive = filter_var($request->get('products.active'), FILTER_VALIDATE_BOOLEAN);
                                $query->where('products.active', $isActive);
                            }

                            // Order by
                            $query->orderBy('products.order', 'ASC')
                                ->orderBy('products.created_at', 'DESC');
                        },
                        'products.translations',
                        'products.productAvatar',
                        'products.vat',
                        /*'products.productLabels' => function ($query) {
                            // Only get active labels
                            $query->where('active', true);
                        },*/
                        // Only get active labels
                        'products.productLabelsActive',
                        'products.allergenens',
                        'products.productFavorites',
                    ]);
                }
            }

            $model = $model->with($with);

            // Search by keyword: name, description
            if ($request->has('keyword') && trim($request->get('keyword') . '') != '') {
                $keyword = $request->get('keyword');

                $model = $model->join('category_translations',
                    'category_translations.category_id', '=', 'categories.id')
                    ->where('category_translations.locale', $locale)
                    ->where(function ($query) use ($keyword) {
                        $query->where('category_translations.name', 'LIKE', "%{$keyword}%")
                            ->orWhere('category_translations.description', 'LIKE', "%{$keyword}%");
                    });
            }

            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->where('categories.workspace_id', $workspaceId);
            }

            // Filter by active status
            if (array_key_exists('active', $arrRequest)) {
                $isActive = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
                $model = $model->where('categories.active', $isActive);
            }

            // Filter by open_timeslots of category
            if (array_key_exists('available_timeslot', $arrRequest)) {
                $isAvailableTimeslot = filter_var($request->get('available_timeslot'), FILTER_VALIDATE_BOOLEAN);

                if ($isAvailableTimeslot) {
                    $model = $model->leftJoin('open_timeslots', function ($join) {
                        $join->on('open_timeslots.foreign_id', '=', 'categories.id')
                            ->where('open_timeslots.foreign_model', Category::class);
                    })
                    ->where(function ($query1) use ($clientTimestamp) {
                        $query1->where('categories.time_no_limit', Category::TIME_LIMIT_NO)
                            ->orWhere(function ($query2) use ($clientTimestamp) {
                                $query2->whereNotNull('open_timeslots.id')
                                    ->where('open_timeslots.day_number', $clientTimestamp->dayOfWeek)
                                    ->where('open_timeslots.status', true)
                                    ->where(function ($query3) use ($clientTimestamp) {
                                        $query3->where('open_timeslots.start_time', '<=', $clientTimestamp->toTimeString())
                                            ->where('open_timeslots.end_time', '>=', $clientTimestamp->toTimeString());
                                    });
                            });
                    });
                }
            }

            // Order by from request
            if (!empty($orderBy)) {
                if ($assocModel->isTranslationAttribute($orderBy)) {
                    // Order by in translation table
                    $model = $model->orderBy('category_translations.' . $orderBy, $sortBy);
                } else {
                    // Order by main table
                    $model = $model->orderBy($assocModel->getTable() . '.' . $orderBy, $sortBy);
                }
            } else {
                // Default order by
                $model = $model
                    ->orderBy($assocModel->getTable() . '.order', 'asc')
                    ->orderBy($assocModel->getTable() . '.created_at', 'desc');
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
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

            //relation
            $model = $model->select(['categories.*'])
                ->withCount(['products'])
                ->with([
                    'categoryOptions',
                    'categoryAvatar',
                    'productSuggestions',
                    'productSuggestions.product',
                    'openTimeslots'
                ])
                ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
                ->where('workspace_id', $workspaceId)
                ->where('category_translations.locale', $locale);

            //search by name
            if ($request->has('keyword_search') && $request->get('keyword_search') != '') {
                $name = $request->get('keyword_search');
                $model = $model->where('category_translations.name', 'LIKE', '%' . $name . '%');
            }

            $model = $model->orderBy('order', 'ASC')->orderBy('created_at', 'ASC');

            return $model;
        });

        return $this->all();
    }

    /**
     * List all product attach by category
     *
     * @param Request $request
     * @param         $workspaceId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getProducts(Request $request, $workspaceId)
    {
        $this->scopeQuery(function (Model $model) use ($request, $workspaceId) {
            $locale = app()->getLocale();

            //relation
            $model = $model->select([
                'products.*',
                'categories.id as category_id',
                'category_translations.name as category_name',
                'product_translations.name as product_name',
                'product_opties.opties_id',
                'product_opties.is_checked',
            ])
            ->where('categories.workspace_id', $workspaceId)
            ->join('category_translations', function ($join) use ($locale){
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', $locale);
            })
            ->leftJoin('products', function ($join) use ($locale){  // leftJoin will show all categories
                $join->on('products.category_id', '=', 'categories.id')
                    ->whereNull('products.deleted_at');
            })
            ->leftJoin('product_translations', function ($join) use ($locale){
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', $locale);
            })
            ->leftJoin('product_opties', 'products.id', '=', 'product_opties.product_id')
            ->orderBy('categories.order', 'ASC')
            ->orderBy('categories.id', 'ASC');

            // search by name
            if ($request->has('keyword_search') && $request->get('keyword_search') != '') {
                $name = $request->get('keyword_search');
                $model = $model->where('product_translations.locale', $locale)
                    ->where('product_translations.name', 'LIKE', '%' . $name . '%');
            }

            // order by naam
            if ($request->has('naam') && $request->get('naam') != '') {
                session()->put('category_id_accordion', $request->category_id_accordion ?: NULL);
                // $model = $model->orderBy('product_translations.name', $request->get('naam'));
            }

            // order by prijs
            if ($request->has('prijs') && $request->get('prijs') != '') {
                session()->put('category_id_accordion', $request->category_id_accordion ?: NULL);
                // $model = $model->orderBy('products.price', $request->get('prijs'));
            }

            $model = $model
                ->orderBy('products.order', 'ASC')
                ->orderBy('products.id', 'ASC');

            return $model;
        });

        return $this->all();
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Category::AVATAR);
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @param int   $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $model = parent::update($attributes, $id);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Category::AVATAR, 'categoryAvatar');
        }

        return $model;
    }

    /**
     * Get first category based on the workspaceid
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getFirstCategory($workspaceId, $param = Cart::TAB_TAKEOUT)
    {
        $this->scopeQuery(function ($model) use ($workspaceId, $param){
            $assocModel = $model->getModel();
            $model = $model->where($assocModel->getTable() . '.workspace_id', $workspaceId)
            ->where($assocModel->getTable() . '.active', 1);
            if ($param == Cart::TAB_LEVERING) {
                $model = $model->where($assocModel->getTable() . '.available_delivery', 1);
            }
            
            $model = $model->orderBy($assocModel->getTable() . '.order', 'ASC');

            return $model;
        });

        return $this->first();
    }

    /**
     * Check available categories
     *
     * @param array $ids
     * @return array
     */
    public function checkAvailable(array $ids)
    {
        $categories = $this->model
            ->whereIn('id', $ids)
            ->pluck('active', 'id');

        $result = [];

        foreach ($ids as $id) {
            $result[$id] = (bool)array_get($categories, $id, false);
        }

        return $result;
    }

    public function connectorCategoryList($limit = 10, $workspaceId = null, $name = null, $updatedSince = null) {
        $categories = $this->makeModel()->with('workspace');

        if(!is_null($workspaceId)) {
            $categories = $categories->whereIn('workspace_id', Workspace::getPrinterGroupWorkspaceIds($workspaceId));
        }

        if(!is_null($name)) {
            $categories = $categories->whereHas('translations', function($query) use ($name) {
                $query->where('name', $name);
            });
        }

        if(!is_null($updatedSince)) {
            $categories = $categories->where('updated_at', '>', $updatedSince);
        }

        return $categories->paginate($limit);
    }
}
