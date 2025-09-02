<?php

namespace App\Http\Controllers\API;

use App\Facades\Helper;
use App\Http\Requests\API\CreateProductAPIRequest;
use App\Http\Requests\API\UpdateProductAPIRequest;
use App\Http\Requests\API\ValidateAvailableDeliveryAPIRequest;
use App\Http\Requests\API\ValidateAvailableTimeslotAPIRequest;
use App\Http\Requests\API\ValidateProductCouponAPIRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ProductController
 * @package App\Http\Controllers\API
 */
class ProductAPIController extends AppBaseController
{
    /** @var ProductRepository $productRepository */
    protected $productRepository;

    /**
     * ProductAPIController constructor.
     * @param ProductRepository $productRepo
     */
    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct();

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

        $this->productRepository->pushCriteria(new RequestCriteria($request));
        $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $products = $this->productRepository->paginate($limit);

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
     * @param CreateProductAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProductAPIRequest $request)
    {
        $input = $request->all();

        $product = $this->productRepository->create($input);

        return $this->sendResponse($product->toArray(), trans('product.message_created_successfully'));
    }

    public function list(Request $request)
    {
        // Get auth from request
        $user = auth('api')->user();
        $loggedIn = !empty($user);
        $favoriteProducts = [];

        $productIds = collect(explode(',', $request->input('ids')))->filter()->values()->all();
        $categoryIds = collect(explode(',', $request->input('category_ids')))->filter()->values()->all();
        $result = $this->productRepository->getProductByIds($request->workspace_id, $productIds, $categoryIds)
            ->map(function ($product) {
                /** @var \App\Models\Product $product */
                return [
                    'id' => $product->id,
                    'created_at' => Helper::getDatetimeFromFormat($product->created_at, 'Y-m-d H:i:s'),
                    'updated_at' => Helper::getDatetimeFromFormat($product->updated_at, 'Y-m-d H:i:s'),
                    'deleted_at' => Helper::getDatetimeFromFormat($product->deleted_at, 'Y-m-d H:i:s'),
                    'active' => (!empty($product->active) && empty($product->deleted_at)),
                    'name' => $product->name, // in the translation table
                    'description' => $product->description, // in the translation table
                    'workspace_id' => $product->workspace_id,
                    'workspace' => $product->workspace->getSummaryInfo(),
                    'category_id' => $product->category_id,
                    'vat_id' => $product->vat_id,
                    'vat' => (!empty($product->vat)) ? $product->vat->getSummaryInfo() : null,
                    'currency' => $product->currency,
                    'price' => $product->price,
                    'use_category_option' => $product->use_category_option,
                    'time_no_limit' => $product->time_no_limit,
                    'is_suggestion' => $product->is_suggestion,
                    'order' => (!empty($product->order)) ? $product->order : 0,
                    'photo' => $product->photo,
                    'photo_path' => $product->photo_path,
                    'allergenens' => $product->allergenens->transform(function ($allergenen) {
                        /** @var \App\Models\Allergenen $allergenen */
                        return (is_array($allergenen)) ? $allergenen : $allergenen->getFullInfo();
                    }),
                    'labels' => $product->productLabelsActive->transform(function ($label) {
                        /** @var \App\Models\ProductLabel $label */
                        return (is_array($label)) ? $label : $label->getFullInfo();
                    }),
                    'productFavorites' => $product->productFavorites,
                    'category' => (!empty($product->category_id) && !empty($product->category)) ? $product->category->getFullInfo() : null,
                    'options' => $product->options->map(function ($option) {
                        /** @var \App\Models\Option $option */
                        return [
                            'id' => $option->id,
                            'min' => $option->min,
                            'max' => $option->max,
                            'type' => $option->type,
                            'type_display' => $option->type_display,
                            'is_ingredient_deletion' => $option->is_ingredient_deletion,
                            'order' => $option->order ?: 0,
                            'name' => $option->name,
                            'items' => $option->items->map(function ($item) {
                                /** @var \App\Models\OptionItem $item */
                                return [
                                    'id' => $item->id,
                                    'name' => $item->name,
                                    'price' => Helper::formatCurrencyNumber($item->price),
                                    'currency' => $item->currency,
                                    'available' => $item->available,
                                    'master' => $item->master,
                                    'order' => $item->order ?: 0,
                                ];
                            }),
                        ];
                    }),
                ];
            });
            
        // When user logged in
        if ($loggedIn) {
            $productIds = collect($result)->pluck('id')->toArray();
            $favoriteProducts = $this->productRepository->checkFavoriteProducts($user, $productIds);
        }
        $result = $result->map(function ($product) use ($favoriteProducts) {
            $product['liked'] = array_get($favoriteProducts, $product['id'], false);

            return $product;
        })->toArray();

        return $this->sendResponse($result, trans('product.message_retrieved_successfully'))
            ->withHeaders([
                'X-Total-Count' => count($result),
            ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Product $product)
    {
        // Get auth from request
        $user = $this->productRepository->getJWTAuth();
        $loggedIn = !empty($user);
        $favoriteProducts = [];

        // When user logged in
        if ($loggedIn) {
            $productIds = [$product->id];
            $favoriteProducts = $this->productRepository->checkFavoriteProducts($user, $productIds);
        }

        $result = $product->getFullInfo();

        if ($loggedIn) {
            $result = array_merge($result, [
                // Like or not
                'liked' => array_get($favoriteProducts, $product->id, false),
            ]);
        }

        // Custom info
        $result = array_merge($result, [
            // Get full info of category
            'category' => (!empty($product->category_id) && !empty($product->category)) ? $product->category->getFullInfo() : null
        ]);

        return $this->sendResponse($result, trans('product.message_retrieved_successfully'));
    }

    /**
     * @param UpdateProductAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Product $product */
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError(trans('product.not_found'));
        }

        $product = $this->productRepository->update($input, $id);

        return $this->sendResponse($product->toArray(), trans('product.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Product $product */
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError(trans('product.not_found'));
        }

        $product->delete();

        return $this->sendResponse($id, trans('product.message_deleted_successfully'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function like(Product $product)
    {
        $user = \Auth::user();
        $liked = $this->productRepository->like($user, $product);
        $result = array_merge($product->getFullInfo(), [
            'liked' => $liked,
        ]);

        return $this->sendResponse($result, trans('product.updated_successfully'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function unlike(Product $product)
    {
        $user = \Auth::user();
        $liked = $this->productRepository->unlike($user, $product);
        $result = array_merge($product->getFullInfo(), [
            'liked' => $liked,
        ]);

        return $this->sendResponse($result, trans('product.updated_successfully'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function toggleLike(Product $product)
    {
        $user = \Auth::user();
        $liked = $this->productRepository->toggleLike($user, $product);
        $result = array_merge($product->getFullInfo(), [
            'liked' => $liked,
        ]);

        return $this->sendResponse($result, trans('product.updated_successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function liked(Request $request)
    {
        // Custom request
        $request->merge([
            'request_type' => 'liked',
        ]);

        $this->productRepository->pushCriteria(new RequestCriteria($request));
        $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $products = $this->productRepository->paginate($limit);

        $products->transform(function ($product) {
            /** @var \App\Models\Product $product */

            // To array
            return array_merge($product->getFullInfo(), [
                // Liked
                'liked' => true,
            ]);
        });
        $result = $products->toArray();

        return $this->sendResponse($result, trans('product.message_retrieved_multiple_successfully'));
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function options(Product $product)
    {
        $options = $this->productRepository->options($product);

        $options->transform(function ($option) {
            /** @var \App\Models\Option $option */

            // To array
            $arrOption = $option->getFullInfo();

            // Custom info
            $arrOption = array_merge($arrOption, [
                'items' => $option->items->transform(function ($item) {
                    /** @var \App\Models\OptionItem $item */
                    return $item->getFullInfo();
                }),
            ]);

            return $arrOption;
        });
        $result = $options->toArray();

        return $this->sendResponse($result, trans('option.message_retrieved_multiple_successfully'));
    }

    /**
     * @param ValidateAvailableDeliveryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAvailableDelivery(ValidateAvailableDeliveryAPIRequest $request)
    {
        $productIds = $request->get('product_id');

        $result = $this->productRepository->validateAvailableDelivery($productIds);

        return $this->sendResponse($result, trans('product.message_validated_successfully'));
    }

    /**
     * @param ValidateAvailableTimeslotAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAvailableTimeslot(ValidateAvailableTimeslotAPIRequest $request)
    {
        $productIds = $request->get('product_id');
        $date = $request->get('date');
        $time = $request->get('time');

        $result = $this->productRepository->validateAvailableTimeslots($productIds, $date, $time, $request->from);

        return $this->sendResponse($result, trans('product.message_validated_successfully'));
    }

    public function validateAvailableTimeslotsDateAndTime(Request $request)
    {
        $productIds = array_filter(explode(',', $request->product_id));
        $date = $request->get('date');
        $time = $request->get('time');

        $result = $this->productRepository->validateAvailableTimeslotsDateAndTime($productIds, $date, $time);

        return $this->sendResponse($result, trans('product.message_validated_successfully'));
    }

    /**
     * Validate timeslot of the product
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateTimeslot(Product $product)
    {
        $result = $this->productRepository->validateTimeslot($product);

        return $this->sendResponse($result, trans('product.message_validated_successfully'));
    }

    /**
     * @param ValidateProductCouponAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCoupon(ValidateProductCouponAPIRequest $request)
    {
        $productIds = $request->get('product_id');
        $code = $request->get('code');

        try {
            // Get auth from request
            $user = $this->productRepository->getJWTAuth();
            $userId = !empty($user) ? $user->id : null;

            $workspaceId = $this->productRepository->getWorkspaceFromProduct($productIds);
            $result = $this->productRepository->validateProductCoupon($productIds, $code, $userId, $workspaceId);

            return $this->sendResponse($result, trans('product.message_validated_successfully'));
        } catch (\Exception $ex) {
            $data = [
                'code' => $ex->getCode(),
            ];
            return $this->sendError($ex->getMessage(), 500, $data);
        }
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

        $result = $this->productRepository->checkAvailable($ids);

        return $this->sendResponse($result, trans('product.message_retrieved_multiple_successfully'));
    }

}
