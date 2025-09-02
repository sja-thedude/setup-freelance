<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\WorkspaceExtra;
use App\Repositories\CartItemRepository;
use App\Repositories\CategoryOptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSuggestionRepository;
use App\Repositories\WorkspaceExtraRepository;
use App\Repositories\WorkspaceRepository;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\CategoryRelationRepository;
use Log;

/**
 * Class CategoryController
 *
 * @package App\Http\Controllers\Manager
 */
class CategoryController extends BaseController
{
    /**
     * @var WorkspaceRepository
     */
    private $workspaceRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * @var CategoryOptionRepository
     */
    private $categoryOptionRepository;

    /**
     * @var OpenTimeslotRepository
     */
    private $openTimeslotRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var WorkspaceExtraRepository
     */
    private $workspaceExtraRepository;

    /**
     * @var ProductSuggestionRepository
     */
    private $productSuggestionRepository;

    /**
     * @var ProductOptionRepository
     */
    private $productOptionRepository;

    /**
     * @var CartItemRepository
     */
    private $cartItemRepository;

    private $categoryRelationRepository;

    /**
     * CategoryController constructor.
     * @param CategoryRepository $categoryRepository
     * @param OptionRepository $optionRepository
     * @param CategoryOptionRepository $categoryOptionRepository
     * @param OpenTimeslotRepository $openTimeslotRepository
     * @param MediaRepository $mediaRepository
     * @param ProductRepository $productRepository
     * @param WorkspaceExtraRepository $workspaceExtraRepository
     * @param ProductSuggestionRepository $productSuggestionRepository
     * @param ProductOptionRepository $productOptionRepository
     * @param CartItemRepository $cartItemRepository
     * @param CategoryRelationRepository $categoryRelationRepository
     */
    public function __construct(
        WorkspaceRepository $workspaceRepository,
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        CategoryOptionRepository $categoryOptionRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        WorkspaceExtraRepository $workspaceExtraRepository,
        ProductSuggestionRepository $productSuggestionRepository,
        ProductOptionRepository $productOptionRepository,
        CartItemRepository $cartItemRepository,
        CategoryRelationRepository $categoryRelationRepository
    ) {
        parent::__construct();

        $this->workspaceRepository = $workspaceRepository;
        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->categoryOptionRepository = $categoryOptionRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->workspaceExtraRepository = $workspaceExtraRepository;
        $this->productSuggestionRepository = $productSuggestionRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->categoryRelationRepository = $categoryRelationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->getAll($request, $this->tmpUser->workspace_id);

        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        return view('manager.categories.index', compact('categories', 'options'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $workspaceId = $this->tmpUser->workspace_id;
        $workspace = $this->workspaceRepository->find($workspaceId);
        $options = $this->optionRepository->getAll($request, $workspaceId);

        $roleAccount = $this->tmpUser->role_id;

        $wSExtraGroupOrder = $this->workspaceExtraRepository->findWhere([
            'workspace_id' => $workspaceId,
            'type'         => WorkspaceExtra::GROUP_ORDER,
        ])->first();

        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $workspaceId]);

        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);

        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = [];
        $enableInHouse = $workspace->enableInHouse();

        return response()->json([
            'code' => 200,
            'data' => view('manager.categories.partials.form', [
                'options'                 => $options,
                'groupOrder'              => $wSExtraGroupOrder,
                'roleAccount'             => $roleAccount,
                'productsGroupByCategory' => $productsGroupByCategory,
                'action'                  => route($this->guard . '.categories.store'),
                'method'                  => 'POST',
                'idForm'                  => 'create_categories',
                'titleModal'              => trans('category.nieuwe_ategorie'),
                'categories' => $categories,
                'categoryIds' => $categoryIds,
                'enableInHouse' => $enableInHouse,
            ])->render(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCategoryRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['files']['file'][] = $request->uploadAvatar ?? "undefined";

            $category = $this->categoryRepository->create($data);

            if (isset($data['orderOptions'])) {
                $categorieOptions = array();
                $orderOptions = \GuzzleHttp\json_decode($data['orderOptions']);
                foreach ($orderOptions as $k => $option) {
                    $categorieOptions[$k]['category_id'] = $category->id;
                    $categorieOptions[$k]['opties_id']   = $option->id;
                    $categorieOptions[$k]['is_checked']  = $option->is_checked;
                }
                $this->categoryOptionRepository->saveMany($categorieOptions);
            }

            if ($category->time_no_limit) {
                $timeSlots = array();
                foreach ($data['days'] as $k => $day) {
                    $day['foreign_model'] = Category::class;
                    $day['workspace_id']  = $category->workspace_id;
                    $day['foreign_id']    = $category->id;
                    $day['status']        = isset($day['status']);
                    $timeSlots[$k]        = $day;
                }
                $this->openTimeslotRepository->saveMany($timeSlots);
            }

            $dataSuggestion = array();
            $productIsSuggestion = array_filter(explode(',', $data['listProduct']));
            if ($productIsSuggestion) {
                foreach ($productIsSuggestion as $k => $id) {
                    $dataSuggestion[$k]['category_id'] = $category->id;
                    $dataSuggestion[$k]['product_id']  = $id;
                }
                $this->productSuggestionRepository->saveMany($dataSuggestion);
            }

            //Save categories
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Category::class, $category->id);
            }

            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($data, trans('category.created_successfully'));
            }

            Flash::success(trans('category.created_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * @param Request $request
     * @param int     $categoryId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $categoryId)
    {
        $category = $this->categoryRepository->findWithoutFail($categoryId);

        if (empty($category)) {
            Flash::error(trans('category.not_found'));
            return redirect(route($this->guard . '.categories.index'));
        }

        $workspaceId = $category->workspace_id;
        $workspace = $category->workspace;
        $category = $this->categoryRepository->with(['categoryOptions', 'categoryOptions.option', 'productSuggestions'])->find($categoryId);

        $options = $this->optionRepository->getAll($request, $workspaceId);

        $roleAccount = $this->tmpUser->role_id;

        $wSExtraGroupOrder = $this->workspaceExtraRepository->findWhere([
            'workspace_id' => $workspaceId,
            'type'         => WorkspaceExtra::GROUP_ORDER,
        ])->first();

        $openTime = $this->openTimeslotRepository->findWhere([
            'foreign_model' => Category::class,
            'foreign_id'    => $category->id,
        ])->keyBy('day_number')->toArray();

        $media = $this->mediaRepository->findWhere([
            'foreign_id' => $categoryId,
            'foreign_model' => Category::class
        ])->first();

        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $workspaceId]);

        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);

        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = $category->categoriesRelation()->pluck('category_id')->toArray();
        $enableInHouse = $workspace->enableInHouse();

        return response()->json([
            'code' => 200,
            'data' => view('manager.categories.partials.form', [
                'options'                 => $options,
                'category'                => $category,
                'media'                   => $media,
                'openTime'                => $openTime,
                'roleAccount'             => $roleAccount,
                'groupOrder'              => $wSExtraGroupOrder,
                'productsGroupByCategory' => $productsGroupByCategory,
                'action'                  => route($this->guard . '.categories.update', $category->id),
                'method'                  => 'PUT',
                'idForm'                  => 'update_categories',
                'titleModal'              => trans('category.categorie_wijzigen'),
                'categories' => $categories,
                'categoryIds' => $categoryIds,
                'enableInHouse' => $enableInHouse,
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param int     $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $categoryId)
    {
        $category = $this->categoryRepository->with(['products'])->findWithoutFail($categoryId);

        $productsBelongOptionCategory = $category->products->where('use_category_option', 0);

        if (empty($category)) {
            Flash::error(trans('category.not_found'));
            return redirect(route($this->guard . '.categories.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            if (isset($request->uploadAvatar)) {
                $data['files']['file'][] = $request->uploadAvatar;
            }

            $category = $this->categoryRepository->update($data, $categoryId);
            $this->productRepository->updateAndWhere(['category_id' => $categoryId], ['time_no_limit' => $category->time_no_limit]);
            $productIds = $category->products->pluck('id')->toArray();

            // Option of category
            if (isset($data['orderOptions'])) {
                $productOptions = array();
                $categorieOptions = array();
                $orderOptions = \GuzzleHttp\json_decode($data['orderOptions']);

                foreach ($orderOptions as $k => $option) {
                    $categorieOptions[$k]['category_id'] = $category->id;
                    $categorieOptions[$k]['opties_id']   = $option->id;
                    $categorieOptions[$k]['is_checked']  = $option->is_checked;

                    // Auto get option cate for product
                    foreach ($productsBelongOptionCategory as $t => $product) {
                        $productOptions[$k . '_' . $t]['product_id'] = $product->id;
                        $productOptions[$k . '_' .  $t]['opties_id']  = $option->id;
                        $productOptions[$k . '_' . $t]['is_checked'] = $option->is_checked;

                        $this->productOptionRepository->deleteWhere(['product_id' => $product->id]);
                    }
                }

                $this->categoryOptionRepository->deleteWhere(['category_id' => $category->id]);
                $this->categoryOptionRepository->saveMany($categorieOptions);

                // Auto save option cate for product
                $this->productOptionRepository->saveMany($productOptions);
            }

            // Timeslot for category
            if ($category->time_no_limit) {
                $timeSlots = array();
                foreach ($data['days'] as $k => $day) {
                    $day['foreign_model'] = Category::class;
                    $day['workspace_id']  = $category->workspace_id;
                    $day['foreign_id']    = $category->id;
                    $day['status']        = isset($day['status']);
                    $timeSlots[$k]        = $day;
                }
                $this->openTimeslotRepository->deleteWhere([
                    'foreign_model' => Category::class,
                    'foreign_id'    => $category->id
                ]);
                $this->openTimeslotRepository->saveMany($timeSlots);

                // Timeslot for product
                $timeSlotsProduct = array();
                foreach ($productIds as $key => $id) {
                    foreach ($data['days'] as $k => $day) {
                        $day['foreign_model']        = Product::class;
                        $day['workspace_id']         = $category->workspace_id;
                        $day['foreign_id']           = $id;
                        $day['status']               = isset($day['status']);
                        $timeSlotsProduct[$key . $k] = $day;
                    }
                }

                $this->openTimeslotRepository->deleteWhereIn([
                    'foreign_model' => Product::class
                ], [
                    'column' => 'foreign_id',
                    'values' => $productIds
                ]);

                $this->openTimeslotRepository->saveMany($timeSlotsProduct);
            }

            $dataSuggestion = array();
            $productIsSuggestion = array_filter(explode(',', $data['listProduct']));

            $this->productSuggestionRepository->deleteWhere(['category_id' => $category->id]);
            if ($productIsSuggestion) {
                foreach ($productIsSuggestion as $k => $id) {
                    $dataSuggestion[$k]['category_id'] = $category->id;
                    $dataSuggestion[$k]['product_id']  = $id;
                }
                $this->productSuggestionRepository->saveMany($dataSuggestion);
            }

            //Save categories
            $this->categoryRelationRepository->deleteWhere(['foreign_model' => Category::class, 'foreign_id' => $category->id]);
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Category::class, $category->id);
            }

            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($data, trans('category.updated_successfully'));
            }

            Flash::success(trans('category.updated_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error(trans('category.not_found'));
            return redirect(route($this->guard . '.categories.index'));
        }

        $this->categoryRepository->delete($id);

        $this->cartItemRepository->deleteWhere([
            'workspace_id' => $this->tmpUser->workspace_id,
            'category_id'  => $id,
        ]);

        $this->productRepository->deleteFavorite($category);

        $this->productRepository->deleteWhere([
            'workspace_id' => $this->tmpUser->workspace_id,
            'category_id'  => $id
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => trans('category.deleted_confirm'),
        ]);
    }

    /**
     * @param int     $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateStatus(int $id, Request $request)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error(trans('category.not_found'));
            return redirect(route($this->guard . '.categories.index'));
        }

        try {
            \DB::beginTransaction();

            $input['active'] = (int) $request->status;
            $data = $this->categoryRepository->with('products')->update($input, $id);

            $this->productRepository->updateAndWhere(['category_id' => $id], ['active' => $input['active']]);

            \DB::commit();

            return response()->json([
                'status' => $data->active,
            ]);

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrder(Request $request)
    {
        try {
            \DB::beginTransaction();

            $orders = $request->order;

            foreach ($orders as $no => $id) {
                $this->categoryRepository->update(['order' => $no + 1], $id);
            }

            \DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Success!',
                'data'    => [],
            ]);

        } catch (\Exception $exc) {
            \DB::rollback();

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }
}
