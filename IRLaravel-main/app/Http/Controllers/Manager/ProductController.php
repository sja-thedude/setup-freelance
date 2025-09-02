<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Allergenen;
use App\Models\Category;
use App\Models\Product;
use App\Models\WorkspaceExtra;
use App\Repositories\CartItemRepository;
use App\Repositories\CategoryOptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductLabelRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\VatRepository;
use App\Repositories\WorkspaceExtraRepository;
use Flash;
use Illuminate\Http\Request;
use Log;

/**
 * Class ProductController
 *
 * @package App\Http\Controllers\Manager
 */
class ProductController extends BaseController
{
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
     * @var ProductOptionRepository
     */
    private $productOptionRepository;

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
     * @var VatRepository
     */
    private $vatRepository;

    /**
     * @var ProductAllergenenRepository
     */
    private $productAllergenenRepository;

    /**
     * @var ProductLabelRepository
     */
    private $productLabelRepository;

    /**
     * @var WorkspaceExtraRepository
     */
    private $workspaceExtraRepository;

    /**
     * @var CartItemRepository
     */
    private $cartItemRepository;

    /**
     * @var SettingConnectorRepository
     */
    private $settingConnectorRepository;

    /**
     * ProductController constructor.
     *
     * @param CategoryRepository          $categoryRepository
     * @param OptionRepository            $optionRepository
     * @param CategoryOptionRepository    $categoryOptionRepository
     * @param ProductOptionRepository     $productOptionRepository
     * @param ProductAllergenenRepository $productAllergenenRepository
     * @param ProductLabelRepository      $productLabelRepository
     * @param OpenTimeslotRepository      $openTimeslotRepository
     * @param MediaRepository             $mediaRepository
     * @param ProductRepository           $productRepository
     * @param VatRepository               $vatRepository
     * @param WorkspaceExtraRepository    $workspaceExtraRepository
     * @param CartItemRepository          $cartItemRepository
     * @param SettingConnectorRepository  $settingConnectorRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        CategoryOptionRepository $categoryOptionRepository,
        ProductOptionRepository $productOptionRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        ProductLabelRepository $productLabelRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        VatRepository $vatRepository,
        WorkspaceExtraRepository $workspaceExtraRepository,
        CartItemRepository $cartItemRepository,
        SettingConnectorRepository $settingConnectorRepository
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->categoryOptionRepository = $categoryOptionRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->productLabelRepository = $productLabelRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->vatRepository = $vatRepository;
        $this->workspaceExtraRepository = $workspaceExtraRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->settingConnectorRepository = $settingConnectorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categoriesWithProducts = $this->categoryRepository
            ->getProducts($request, $this->tmpUser->workspace_id)
            ->groupBy('category_id');

        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        $categoryIdAccordion = session()->has('category_id_accordion') ? session()->get('category_id_accordion') : NULL;
        $this->productRepository->updateOrderForSorting($categoriesWithProducts, $request, $categoryIdAccordion);
        session()->forget('category_id_accordion');

        return view('manager.products.index', compact('categoriesWithProducts', 'options', 'categoryIdAccordion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        $categories = $this->categoryRepository->getAll($request, $this->tmpUser->workspace_id);

        $vats = $this->vatRepository->findWhere(['country_id' => $this->tmpUser->workspace->country_id]);

        $allergenens = Allergenen::all();

        $wSExtraAllergenen = $this->workspaceExtraRepository->findWhere([
            'workspace_id' => $this->tmpUser->workspace_id,
            'type'         => WorkspaceExtra::ALLERGENEN,
        ])->first();

        $connectorsList = $this->getConnectorsList();

        return response()->json([
            'code' => 200,
            'data' => view('manager.products.partials.form', [
                'vats'              => $vats,
                'wSExtraAllergenen' => $wSExtraAllergenen,
                'options'           => $options,
                'categories'        => $categories,
                'allergenens'       => $allergenens,
                'action'            => route($this->guard . '.products.store'),
                'method'            => 'POST',
                'idForm'            => 'create_product',
                'titleModal'        => trans('product.nieuwe_ategorie'),
                'connectorsList'    => $connectorsList
            ])->render(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProductRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['files']['file'][] = $request->uploadAvatar ?? "undefined";

            $product = $this->productRepository->create($data);

            if (isset($data['orderOptions'])) {
                $orderProducts = array();
                $orderOptions = \GuzzleHttp\json_decode($data['orderOptions']);
                foreach ($orderOptions as $k => $option) {
                    $orderProducts[$k]['product_id'] = $product->id;
                    $orderProducts[$k]['opties_id']  = $option->id;
                    $orderProducts[$k]['is_checked'] = $option->is_checked;
                }
                $this->productOptionRepository->saveMany($orderProducts);
            }

            if (isset($data['allergenens'])) {
                $productAllergenens = array();
                foreach ($data['allergenens'] as $k => $idAller) {
                    $productAllergenens[$k]['product_id']    = $product->id;
                    $productAllergenens[$k]['allergenen_id'] = $idAller;
                }
                $this->productAllergenenRepository->saveMany($productAllergenens);
            }

            $this->productLabelRepository->saveMany([
                ['product_id' => $product->id, 'type' => $data['veggie'], 'active' => !$data['veggie'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['vegan'],  'active' => !$data['vegan'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['spicy'],  'active' => !$data['spicy'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['new'],    'active' => !$data['new'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['promo'],  'active' => !$data['promo'] ?: 1],
            ]);

            if ($product->time_no_limit && isset($data['days'])) {
                $timeSlots = array();
                foreach ($data['days'] as $k => $day) {
                    $day['foreign_model'] = Product::class;
                    $day['workspace_id']  = $product->workspace_id;
                    $day['foreign_id']    = $product->id;
                    $day['status']        = isset($day['status']);
                    $timeSlots[$k]        = $day;
                }
                $this->openTimeslotRepository->saveMany($timeSlots);
            }

            $connectorsList = $this->getConnectorsList();
            $this->productRepository->updateOrCreateProductReferences($product, $data, $this->tmpWorkspace->id, $connectorsList);

            \DB::commit();

            session()->put('category_id_accordion', $product->category_id);

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('product.created_successfully'));
            }

            Flash::success(trans('product.created_successfully'));

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
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int     $productId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $productId)
    {
        $product = $this->productRepository->findWithoutFail($productId);

        if (empty($product)) {
            Flash::error(trans('product.not_found'));
            return redirect(route($this->guard . '.products.index'));
        }

        $product = $this->productRepository->with([
            'productOptions',
            'productAllergenens',
            'productLabels',
            'productOptions.option',
            'category.openTimeslots'
        ])->find($productId);

        $categories = $this->categoryRepository->getAll($request, $this->tmpUser->workspace_id);

        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        $vats = $this->vatRepository->findWhere(['country_id' => $this->tmpUser->workspace->country_id]);

        $timeNoLimitCategory = $product->category->time_no_limit;

        $allergenens = Allergenen::all();

        $wSExtraAllergenen = $this->workspaceExtraRepository->findWhere([
            'workspace_id' => $this->tmpUser->workspace_id,
            'type'         => WorkspaceExtra::ALLERGENEN,
        ])->first();

        $connectorsList = $this->getConnectorsList();

        $productReferences = null;
        if(!empty($connectorsList)) {
            $productReferences = $this->productRepository->getProductReferencesByWorkspaceAndLocalId($this->tmpWorkspace->id, $product->id);
            $productReferences = $productReferences->keyBy('provider');
        }

        $openTime = $this->openTimeslotRepository->findWhere([
            'foreign_model' => Product::class,
            'foreign_id'    => $product->id,
        ])->keyBy('day_number')->toArray();

        $openTimeRootCategory = $product->category->openTimeslots->map
            ->only(['day_number', 'status', 'start_time_convert', 'end_time_convert'])
            ->keyBy('day_number')
            ->toArray();

        $media = $this->mediaRepository->findWhere([
            'foreign_id'    => $productId,
            'foreign_model' => Product::class,
        ])->first();

        return response()->json([
            'code' => 200,
            'data' => view('manager.products.partials.form', [
                'openTime'             => $openTime,
                'media'                => $media,
                'timeNoLimitCategory'  => $timeNoLimitCategory,
                'vats'                 => $vats,
                'wSExtraAllergenen'    => $wSExtraAllergenen,
                'openTimeRootCategory' => $openTimeRootCategory,
                'options'              => $options,
                'product'              => $product,
                'categories'           => $categories,
                'allergenens'          => $allergenens,
                'action'               => route($this->guard . '.products.update', $product->id),
                'method'               => 'PUT',
                'idForm'               => 'update_product',
                'titleModal'           => trans('product.categorie_wijzigen'),
                'connectorsList'       => $connectorsList,
                'productReferences'    => $productReferences
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param int                  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, int $productId)
    {
        $product = $this->productRepository->findWithoutFail($productId);

        if (empty($product)) {
            Flash::error(trans('product.not_found'));
            return redirect(route($this->guard . '.products.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            if (isset($request->uploadAvatar)) {
                $data['files']['file'][] = $request->uploadAvatar;
            }

            $product = $this->productRepository->update($data, $productId);

            if (isset($data['orderOptions'])) {
                $orderProducts = array();
                $orderOptions = \GuzzleHttp\json_decode($data['orderOptions']);
                foreach ($orderOptions as $k => $option) {
                    $orderProducts[$k]['product_id'] = $product->id;
                    $orderProducts[$k]['opties_id']  = $option->id;
                    $orderProducts[$k]['is_checked'] = $option->is_checked;
                }
                $this->productOptionRepository->deleteWhere(['product_id' => $product->id]);
                $this->productOptionRepository->saveMany($orderProducts);
            }

            $this->productAllergenenRepository->deleteWhere(['product_id' => $product->id]);
            if (isset($data['allergenens'])) {
                $productAllergenens = array();
                foreach ($data['allergenens'] as $k => $idAller) {
                    $productAllergenens[$k]['product_id']    = $product->id;
                    $productAllergenens[$k]['allergenen_id'] = $idAller;
                }
                $this->productAllergenenRepository->saveMany($productAllergenens);
            }

            $this->productLabelRepository->deleteWhere(['product_id' => $product->id]);
            $this->productLabelRepository->saveMany([
                ['product_id' => $product->id, 'type' => $data['veggie'], 'active' => !$data['veggie'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['vegan'],  'active' => !$data['vegan'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['spicy'],  'active' => !$data['spicy'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['new'],    'active' => !$data['new'] ?: 1],
                ['product_id' => $product->id, 'type' => $data['promo'],  'active' => !$data['promo'] ?: 1],
            ]);

            if ($product->time_no_limit && isset($data['days'])) {
                $timeSlots = array();
                foreach ($data['days'] as $k => $day) {
                    $day['foreign_model'] = Product::class;
                    $day['workspace_id']  = $product->workspace_id;
                    $day['foreign_id']    = $product->id;
                    $day['status']        = isset($day['status']);
                    $timeSlots[$k]        = $day;
                }
                $this->openTimeslotRepository->deleteWhere([
                    'foreign_model' => Product::class,
                    'foreign_id'    => $product->id,
                ]);
                $this->openTimeslotRepository->saveMany($timeSlots);
            }

            $connectorsList = $this->getConnectorsList();
            $this->productRepository->updateOrCreateProductReferences($product, $data, $this->tmpWorkspace->id, $connectorsList);

            \DB::commit();

            session()->put('category_id_accordion', $product->category_id);

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('product.updated_successfully'));
            }

            Flash::success(trans('product.updated_successfully'));

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
        $category = $this->productRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error(trans('product.not_found'));
            return redirect(route($this->guard . '.products.index'));
        }

        $this->productRepository->delete($id);

        $this->cartItemRepository->deleteWhere([
            'workspace_id' => $this->tmpUser->workspace_id,
            'product_id'   => $id,
        ]);

        return response()->json([
            'success' => 'success',
            'message' => trans('product.deleted_confirm'),
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
        $category = $this->productRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error(trans('product.not_found'));
            return redirect(route($this->guard . '.products.index'));
        }

        $input['active'] = (int) $request->status;
        $data = $this->productRepository->update($input, $id);

        return response()->json([
            'status' => $data->active,
        ]);
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
                $this->productRepository->update(['order' => $no + 1], $id);
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

    /**
     * @param Request $request
     * @param         $categoryId
     * @param         $useCategoryOption
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getOptionByCategory(Request $request, $categoryId, $useCategoryOption)
    {
        $numberChecked = 0;
        $listOptions = array();

        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        $category = $this->categoryRepository
            ->with('openTimeslots')
            ->findWhere(['id' => $categoryId])
            ->first();

        $timeSlots = $category->openTimeslots->map
            ->only(['day_number', 'status', 'start_time_convert', 'end_time_convert'])
            ->keyBy('day_number')
            ->toArray();

        if (!$useCategoryOption) {
            $categoryOpties = $this->categoryOptionRepository->findWhere(['category_id' => $categoryId]);

            foreach ($categoryOpties as $item) {
                if ($item->is_checked) {
                    $numberChecked++;
                }
                $item->option->is_checked = $item->is_checked;
                $listOptions[$item->option->id] = $item->option;
            }
        }

        foreach ($options as $option) {
            if (array_key_exists($option->id, $listOptions)) {
                continue;
            }
            $option->is_checked = 0;
            $listOptions[$option->id] = $option;
        }

        $html = "";
        foreach ($listOptions as $id => $option) {
            $html .= view('manager.partials.item-option-product', [
                'useCategoryOption' => $useCategoryOption,
                'optionId'          => $id,
                'isChecked'         => $option->is_checked,
                'optionName'        => $option->name,
            ])->render();
        }

        return response()->json([
            'code' => 200,
            'data' => [
                'dropdown'            => $html,
                'numberChecked'       => $numberChecked,
                'timeSlots'           => json_encode($timeSlots),
                'timeNoLimitCategory' => $category->time_no_limit,
            ],
        ]);
    }

    public function updatePrice(UpdateProductRequest $request, int $productId)
    {
        $product = $this->productRepository->findWithoutFail($productId);

        if (empty($product)) {
            Flash::error(trans('product.not_found'));
            return redirect(route($this->guard . '.products.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['use_category_option'] = $product->use_category_option;

            $product = $this->productRepository->update($data, $productId);

            \DB::commit();

            session()->put('category_id_accordion', $product->category_id);

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('product.updated_successfully'));
            }

            Flash::success(trans('product.updated_successfully'));

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
     * @return mixed|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }
}
