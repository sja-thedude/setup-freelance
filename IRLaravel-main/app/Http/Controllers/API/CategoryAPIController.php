<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCategoryAPIRequest;
use App\Http\Requests\API\UpdateCategoryAPIRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CategoryController
 * @package App\Http\Controllers\API
 */
class CategoryAPIController extends AppBaseController
{
    /** @var CategoryRepository $categoryRepository */
    protected $categoryRepository;

    /** @var ProductRepository $productRepository */
    protected $productRepository;

    /**
     * CategoryAPIController constructor.
     * @param CategoryRepository $categoryRepo
     * @param ProductRepository $productRepo
     */
    public function __construct(CategoryRepository $categoryRepo, ProductRepository $productRepo)
    {
        parent::__construct();

        $this->categoryRepository = $categoryRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Custom request
        $request->merge([
            'active' => true,
        ]);

        $this->categoryRepository->pushCriteria(new RequestCriteria($request));
        $this->categoryRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $categories = $this->categoryRepository->paginate($limit);

        $categories->transform(function ($item) {
            /** @var \App\Models\Category $item */
            return $item->getFullInfo();
        });
        $result = $categories->toArray();

        return $this->sendResponse($result, trans('category.message_retrieved_multiple_successfully'));
    }

    /**
     * @param CreateCategoryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCategoryAPIRequest $request)
    {
        $input = $request->all();

        $category = $this->categoryRepository->create($input);

        return $this->sendResponse($category->toArray(), trans('category.created_successfully'));
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        $result = $category->getFullInfo();

        return $this->sendResponse($result, trans('category.message_retrieved_successfully'));
    }

    /**
     * @param UpdateCategoryAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Category $category */
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError(trans('category.not_found'));
        }

        $category = $this->categoryRepository->update($input, $id);

        return $this->sendResponse($category->toArray(), trans('category.updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Category $category */
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError(trans('category.not_found'));
        }

        $category->delete();

        return $this->sendResponse($id, trans('category.deleted_successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        // Custom request
        $request->merge([
            'active' => true,
            'with' => 'products',
            'products.active' => true,
            /*// SonTT: Disable this feature
            // Only filter by active status
            'available_timeslot' => true,*/
        ]);

        $this->categoryRepository->pushCriteria(new RequestCriteria($request));
        $this->categoryRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $categories = $this->categoryRepository->paginate($limit);

        // Get auth from request
        $user = $this->categoryRepository->getJWTAuth();
        $loggedIn = !empty($user);

        $categories->transform(function ($category) use ($user, $loggedIn) {
            /** @var \App\Models\Category $category */

            $products = $category->products;
            $favoriteProducts = [];

            // When user logged in
            if ($loggedIn) {
                $productIds = $products->pluck('id')->toArray();
                $favoriteProducts = $this->productRepository->checkFavoriteProducts($user, $productIds);
            }

            return array_merge($category->getFullInfo(), [
                'products' => $products->transform(function ($product) use ($category, $loggedIn, $favoriteProducts) {
                    /** @var \App\Models\Product $product */

                    // Set relations
                    $product->setRelation('workspace', $category->workspace);
                    $product->setRelation('category', $category);

                    // To array
                    $arrProduct = $product->getFullInfo();

                    // Only custom data if is logged in
                    if ($loggedIn) {
                        $arrProduct = array_merge($arrProduct, [
                            // Like or not
                            'liked' => array_get($favoriteProducts, $product->id, false),
                        ]);
                    }

                    return $arrProduct;
                }),
            ]);
        });
        $result = $categories->toArray();

        return $this->sendResponse($result, trans('category.message_retrieved_multiple_successfully'));
    }

    /**
     * @see \App\Http\Controllers\API\ProductAPIController::index()
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function getSuggestionProducts(Request $request, Category $category)
    {
        $request->merge([
            'active' => true,
            // Suggestion products by category
            'is_suggestion' => true,
            'category_id' => $category->id,
        ]);

        $this->productRepository->pushCriteria(new RequestCriteria($request));
        $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $products = $this->productRepository->paginate($limit, '*', 'paginate', $category);

        // Get auth from request
        $user = $this->productRepository->getJWTAuth();
        $loggedIn = !empty($user);
        $favoriteProducts = [];

        // When user logged in
        if ($loggedIn) {
            $productIds = $products->pluck('id')->toArray();
            $favoriteProducts = $this->productRepository->checkFavoriteProducts($user, $productIds);
        }

        $products->transform(function ($product) use ($loggedIn, $favoriteProducts) {
            /** @var \App\Models\Product $product */

            // To array
            $arrProduct = $product->getFullInfo();

            // Only custom data if is logged in
            if ($loggedIn) {
                $arrProduct = array_merge($arrProduct, [
                    // Like or not
                    'liked' => array_get($favoriteProducts, $product->id, false),
                ]);
            }

            return $arrProduct;
        });
        $result = $products->toArray();

        return $this->sendResponse($result, trans('product.message_retrieved_multiple_successfully'));
    }

    /**
     * Check available categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailable(Request $request)
    {
        $ids = $request->get('id');

        if (!is_array($ids)) {
            // Convert to array
            $ids = explode(',', $ids . '');
        }

        $result = $this->categoryRepository->checkAvailable($ids);

        return $this->sendResponse($result, trans('category.message_retrieved_multiple_successfully'));
    }

}
