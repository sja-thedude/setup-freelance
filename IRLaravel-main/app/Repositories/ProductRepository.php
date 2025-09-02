<?php

namespace App\Repositories;

use App\Helpers\Helper;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReference;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ProductRepository.
 *
 * @package namespace App\Repositories;
 */
class ProductRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'category_id',
        'vat_id',
        'currency',
        'price',
        'use_category_option',
        'time_no_limit',
        'is_suggestion',
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
        return Product::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param null $limit
     * @param array $columns
     * @param string $method
     * @param null $currentModel
     * @return mixed
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate", $currentModel = null)
    {
        $request = request();
        $arrRequest = $request->all();
        $locale = \App::getLocale();
        $user = $this->getJWTAuth();

        $this->scopeQuery(function ($model) use ($request, $arrRequest, $locale, $user, $currentModel) {
            /** @var \Illuminate\Database\Eloquent\Builder $model */

            /** @var \App\Models\Product $assocModel */
            $assocModel = $model->getModel();
            // Get order by from request
            list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

            // Prevent duplicate field
            $model = $model->select('products.*')
                // with relations
                ->with([
                    'translations',
                    'productAvatar',
                    'workspace',
                    'category',
                    'openTimeslots',
                    'productAllergenens',
                    'category.translations',
                    'vat',
                    'productLabels' => function ($query) {
                        // Only get active labels
                        $query->where('active', true);
                    }
                ])
                // Join with related tables
                ->join('product_translations', function ($join) use ($locale) {
                    $join->on('product_translations.product_id', '=', 'products.id')
                        ->where('product_translations.locale', $locale);
                })
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->whereNull('categories.deleted_at');

            // Search by keyword: name, description
            if ($request->has('keyword') && trim($request->get('keyword') . '') != '') {
                $keyword = $request->get('keyword');

                $model = $model->where(function ($query) use ($keyword) {
                        $query->where('product_translations.name', 'LIKE', "%{$keyword}%")
                            ->orWhere('product_translations.description', 'LIKE', "%{$keyword}%");
                    });
            }

            // Filter by workspace
            if ($request->has('workspace_id')) {
                $workspaceId = (int)$request->get('workspace_id');

                $model = $model->where('products.workspace_id', $workspaceId);
            }

            // Filter by category
            if ($request->has('category_id')) {
                $categoryId = (int)$request->get('category_id');

                // Suggestion product by category or not
                if (array_key_exists('is_suggestion', $arrRequest)) {
                    $isSuggestion = filter_var($request->get('is_suggestion'), FILTER_VALIDATE_BOOLEAN);

                    if ($isSuggestion && $currentModel) {
                        $productIds = $currentModel->productSuggestions->pluck('product_id')->toArray();
                        $categoryIds = $currentModel->categoriesRelation->pluck('category_id')->toArray();

                        $model = $model->where(function ($subQuery) use ($categoryIds, $productIds){
                            $subQuery->whereIn('products.category_id', $categoryIds);
                            $subQuery->orWhereIn('products.id', $productIds);
                        });
                    }
                } else {
                    // Filter by category in the main table
                    $model = $model->where('products.category_id', $categoryId);
                }
            }

            // Get favorite products
            if ($request->has('request_type') && $request->has('request_type') == 'liked') {
                if (!empty($user)) {
                    $model = $model->join('product_favorites', 'product_favorites.product_id', '=', 'products.id')
                        ->where('product_favorites.user_id', $user->id)
                        ->groupBy('products.id');
                } else {
                    // Return empty list in the logout status
                    $model = $model->where('products.id', 0);
                }
            }

            // Filter by active status
            if (array_key_exists('active', $arrRequest)) {
                $isActive = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
                $model = $model->where('products.active', $isActive);
            }

            // Order by from request
            if (!empty($orderBy)) {
                if ($assocModel->isTranslationAttribute($orderBy)) {
                    // Order by in translation table
                    $model = $model->orderBy('product_translations.' . $orderBy, $sortBy);
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
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Product::AVATAR);
        }

        return $model;
    }

    public function getProductByIds($workspaceId, $ids, $categoryIds)
    {
        $model = $this->makeModel()
            ->where('active', true)
            ->where('workspace_id', $workspaceId);
        if (count($ids)) {
            $model = $model->whereIn('id', $ids);
        }
        if (count($categoryIds)) {
            $model = $model->whereIn('category_id', $categoryIds);
        }
        $model = $model->select('products.*')
            ->with([
                'workspace',
                'vat',
                'productLabelsActive',
                'category',
                'allergenens',
                'translations',
                'productAvatar',
                'options' => function ($query) {
                    $query->with(['translations', 'items' => function ($query) {
                        $query->orderBy('optie_items.order', 'ASC');
                    }])->wherePivot('is_checked', true);
                },
            ]);

        //Sort
        $sortBy = request('sort_by', 'id');
        $orderBy = request('order_by', 'asc');

        $model = $model->orderBy($sortBy, $orderBy)->get();

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
            $this->attachFiles($model, $attributes['files'], Product::AVATAR, 'productAvatar');
        }

        return $model;
    }

    /**
     * @param $input
     * @param $workspaceId
     * @param $connectorsList
     * @return bool|null
     */
    public function updateOrCreateProductReferences(Product $product, $input, $workspaceId, $connectorsList) {
        if(empty($connectorsList)) {
            return null;
        }

        foreach($connectorsList as $connectorItem) {
            // Get order reference..
            $productReference = $product->productReferences()
                ->where('provider', $connectorItem->provider)
                ->where('local_id', $product->id)
                ->first();

            if(empty($productReference)) {
                $productReference = new ProductReference();
                $productReference->workspace_id = $workspaceId;
                $productReference->local_id = $product->id;
                $productReference->provider = $connectorItem->provider;
            }

            if(!empty($input['productReferences'][$connectorItem->id])) {
                $productReference->remote_id = !empty($input['productReferences'][$connectorItem->id]['remote_id'])
                    ? $input['productReferences'][$connectorItem->id]['remote_id']
                    : '';
                $productReference->save();
            }
        }

        return true;
    }

    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getProductReferencesByWorkspaceAndLocalId($workspaceId, $localId) {
        return ProductReference::where('workspace_id', $workspaceId)
            ->where('local_id', $localId)
            ->get();
    }

    /**
     * @param $provider
     * @param $localIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProductReferencesByWorkspaceAndProviderAndLocalIds($workspaceId, $provider, $localIds) {
        return ProductReference::where('workspace_id', $workspaceId)
            ->where('provider', $provider)
            ->whereIn('local_id', $localIds)
            ->get();
    }

    /**
     * When a use like a product
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function like(User $user, Product $product)
    {
        $liked = $product->productFavorites()
            ->where('user_id', $user->id)
            ->count() > 0;

        if (!$liked) {
            $product->productFavorites()
                ->attach([$user->id]);
            $liked = true;
        }

        return $liked;
    }

    /**
     * When a use like a product
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function unlike(User $user, Product $product)
    {
        $liked = $product->productFavorites()
            ->where('user_id', $user->id)
            ->count() > 0;

        if ($liked) {
            $product->productFavorites()
                ->detach([$user->id]);
            $liked = false;
        }

        return $liked;
    }

    /**
     * When a use like a product
     *
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function toggleLike(User $user, Product $product)
    {
        $liked = $product->productFavorites()
            ->where('user_id', $user->id)
            ->count() > 0;

        if ($liked) {
            $product->productFavorites()
                ->detach([$user->id]);
            $liked = false;
        } else {
            $product->productFavorites()
                ->attach([$user->id]);
            $liked = true;
        }

        return $liked;
    }

    /**
     * @param User $user
     * @param array $productIds
     * @return array
     */
    public function checkFavoriteProducts(User $user, array $productIds)
    {
        $liked = \DB::table('product_favorites')
            ->select('product_id')
            ->selectRaw('COUNT(user_id) AS liked')
            ->whereIn('product_id', $productIds)
            ->where('user_id', $user->id)
            ->groupBy('product_id')
            ->pluck('liked','product_id')
            ->toArray();
        $data = [];

        foreach ($productIds as $productId) {
            $data[$productId] = (int)array_get($liked, $productId, 0) > 0;
        }

        return $data;
    }

    /**
     * @param Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function options(Product $product)
    {
        $options = $product->options()
            ->with(['translations', 'workspace', 'items' => function ($query) {
                $query->orderBy('optie_items.order', 'ASC');
            }])
            ->wherePivot('is_checked', true)
            /*->orderBy('opties.order', 'ASC')*/
            ->get();

        return $options;
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function validateAvailableDelivery(array $productIds)
    {
        $result = [];

        $validate = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('products.id', $productIds)
            ->pluck('categories.available_delivery', 'products.id')
            ->toArray();

        foreach ($productIds as $productId) {
            $result[$productId] = !empty(array_get($validate, $productId, 0));
        }

        return $result;
    }

    /**
     * Validate available timeslot with date and time
     *
     * @param array $productIds
     * @param string $date Date format: Y-m-d
     * @param string $time Time format: H:i
     * @return array
     */
    public function validateAvailableTimeslotsDateAndTime(array $productIds, string $date, string $time)
    {
        $calendarDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
        $dayNumber = $calendarDate->dayOfWeek ?: 7;

        $result = [];

        $productIds = Product::whereIn('id', $productIds)
            ->active()
            ->select(['id', 'active', 'time_no_limit', 'category_id'])
            ->get();
        $availableProducts = \App\Models\OpenTimeslot::where('foreign_model', Product::class)
                ->whereIn('foreign_id', $productIds->pluck('id'))
                ->where('status', true)
                ->where('day_number', $dayNumber)
                ->where('start_time', '<=', $time . ':00')
                ->where('end_time', '>=', $time . ':00')
                ->pluck('foreign_id');
        foreach ($productIds as $product) {
            // Product can be either always available (time_no_limit = 0) or available in specified timeslot
            $result[$product->id] = $product->time_no_limit == 0 || $availableProducts->contains($product->id);
        }

        return $result;
    }

    /**
     * Validate available timeslot by multiple products
     *
     * @param array $productIds
     * @param string|null $date Date format: Y-m-d
     * @param string|null $time Time format: H:i
     * @param string|null $from mobile: Include check category availability
     * @return array
     */
    public function validateAvailableTimeslots(array $productIds, string $date = null, string $time = null, string $from = null)
    {
        $result = [];

        foreach ($productIds as $productId) {
            $result[$productId] = $this->validateAvailableTimeslot($productId, $date, $time, $from);
        }

        return $result;
    }

    /**
     * Validate available timeslot by single product
     *
     * @param int $productId Product ID
     * @param string|null $date Date format: Y-m-d
     * @param string|null $time Time format: H:i
     * @param string|null $from mobile: Include check category availability
     * @return bool
     */
    public function validateAvailableTimeslot(int $productId, string $date = null, string $time = null, string $from = null)
    {
        /** @var \App\Models\Product $product */
        $product = \App\Models\Product::where('id', $productId)
            ->active()
            ->first();

        if (empty($product)) {
            return false;
        }

        // Always available
        if (!$product->time_no_limit) {
            return true;
        }

        // When date is null
        if (empty($date)) {
            return $this->validateTimeslot($product);
        }

        // When date is not null
        $calendarDate = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
        $dayNumber = $calendarDate->dayOfWeek;

        // ThinhPham start day of week from 1 -> 7
        if ($dayNumber === 0) {
            $dayNumber = 7;
        }

        // Check has settings
        $hasTimeslot = \App\Models\OpenTimeslot::where('workspace_id', $product->workspace_id)
                ->where('foreign_model', Product::class)
                ->where('foreign_id', $product->id)
                ->where('status', true)
                ->where('day_number', $dayNumber);

        // When time is not null
        if (!empty($time)) {
            $hasTimeslot->where(function ($query) use ($time) {
                $query->where('start_time', '<=', $time . ':00')
                    ->where('end_time', '>=', $time . ':00');
            });
        }

        $available = $hasTimeslot->count() > 0;

        if ($from) {
            // Check has settings
            $hasTimeslot = \App\Models\OpenTimeslot::where('workspace_id', $product->workspace_id)
                    ->where('foreign_model', Category::class)
                    ->where('foreign_id', $product->category_id)
                    ->where('status', true)
                    ->where('day_number', $dayNumber);

            // When time is not null
            if (!empty($time)) {
                $hasTimeslot->where(function ($query) use ($time) {
                    $query->where('start_time', '<=', $time . ':00')
                        ->where('end_time', '>=', $time . ':00');
                });
            }

            $available = $available && $hasTimeslot->count() > 0;
        }

        return $available;
    }

    /**
     * Validate time slot of the product
     *
     * @param Product $product
     * @return bool
     */
    public function validateTimeslot(Product $product)
    {
        // Priority from products table
        if (!$product->time_no_limit) {
            return true;
        }

        // Check has settings
        $hasTimeslot = \App\Models\OpenTimeslot::where('workspace_id', $product->workspace_id)
            ->where('foreign_model', Product::class)
            ->where('foreign_id', $product->id)
            ->where('status', true)
            ->count() > 0;

        return $hasTimeslot;
    }

    /**
     * Validate coupon by products
     * @param array $productIds
     * @param string $code
     * @param null $userId
     * @param null $workspaceId
     * @return mixed
     * @throws \Exception
     */
    public function validateProductCoupon(array $productIds, string $code, $userId = null, $workspaceId = null)
    {
        /** @var \App\Models\Coupon $coupon */
        $queryCoupon = \App\Models\Coupon::where('code', $code);
        if (!empty($workspaceId)) {
            $queryCoupon->where('workspace_id', $workspaceId);
        }

        $coupon = $queryCoupon
            //->active() // Active = invisible in view
            ->first();
        $couponUsedFromOrderStatus = Order::getCouponUsedFromOrderStatus();

        // Not found coupon by code
        if (empty($coupon)) {
            throw new \Exception(trans('coupon.message_code_invalid'), ERROR_COUPON_CODE_INVALID);
        }

        // Invalid coupon
        if ($coupon->expire_time->lessThan(\Carbon\Carbon::now())) {
            throw new \Exception(trans('coupon.message_coupon_expired'), ERROR_COUPON_EXPIRED);
        }

        // Check number of used
        $numberOfUsed = Order::where('coupon_id', $coupon->id)
            ->whereIn('status', $couponUsedFromOrderStatus)
            ->count();

        if ($numberOfUsed >= $coupon->max_time_all) {
            throw new \Exception(trans('coupon.message_coupon_max_time_all'), ERROR_COUPON_MAX_TIME_ALL);
        }

        // Check number of used by me
        if (!empty($userId)) {
            $numberOfUsedByMe = Order::where('coupon_id', $coupon->id)
                ->whereIn('status', $couponUsedFromOrderStatus)
                ->where('user_id', $userId)
                ->count();

            if ($numberOfUsedByMe >= $coupon->max_time_single) {
                throw new \Exception(trans('coupon.message_coupon_max_time_single'), ERROR_COUPON_MAX_TIME_SINGLE);
            }
        }

        // Validate coupon by product list
        $validProducts = \App\Models\CouponProduct::where('coupon_id', $coupon->id)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        // Apply for categories
        $validProducts = Helper::getCategoryIds($coupon, $validProducts);

        foreach ($productIds as $productId) {
            $result[$productId] = in_array($productId, $validProducts);
        }

        return $result;
    }

    /**
     * Check available categories
     *
     * @param array $ids
     * @return array
     */
    public function checkAvailable(array $ids)
    {
        $products = $this->model
            ->whereIn('id', $ids)
            ->pluck('active', 'id');

        $result = [];

        foreach ($ids as $id) {
            $result[$id] = (bool)array_get($products, $id, false);
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param $workspaceId
     * @param $user
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getProductFavourites(Request $request, $workspaceId, $user)
    {
        $model = $this->makeModel();

        // Prevent duplicate field
        $model = $model->select('products.*')
            // with relations
            ->with([
                'translations',
                'productAvatar',
                'workspace',
                'category',
                'openTimeslots',
                'productAllergenens',
                'category.translations',
                'vat',
                'productLabels' => function ($query) {
                    // Only get active labels
                    $query->where('active', true);
                }
            ])
            // Join with related tables
            ->join('product_translations',
                'product_translations.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id');

        $model = $model->where('products.workspace_id', $workspaceId)
                        ->whereNull('categories.deleted_at');
        // Search by name
//        if (!empty($request->keyword)) {
//            $model = $model->where(function ($query) use ($request) {
//                $query->where('title', 'LIKE', '%' . $request->keyword . '%');
//            });
//        }
        $model = $model->join('product_favorites', 'product_favorites.product_id', '=', 'products.id')
                        ->where('product_favorites.user_id', !empty($user) ? $user->id : null)
                        ->groupBy('products.id');
        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        $model = $model->orderBy($sortBy, $orderBy)->get();

        return $model;
    }

    /**
     * Update order field after sorting
     *
     * @param $categoriesWithProducts
     * @param Request $request
     * @param $categoryIdAccordion
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrderForSorting($categoriesWithProducts, Request $request, $categoryIdAccordion)
    {
        $naam = $request->has('naam');
        $prijs = $request->has('prijs');
        if (empty($naam) && empty($prijs)) {
            return false;
        }

        $orderedProducts = [];
        foreach ($categoriesWithProducts as $categoryId => $products) {
            if ($categoryId == $categoryIdAccordion) {
                $orderedProducts = $this->getOrder($products, $request);
            }
        }

        if (count($orderedProducts) == 0) {
            return $orderedProducts;
        }

        $orderedProductIds = $orderedProducts->pluck('id')->unique()->toArray();
        foreach ($orderedProductIds as $no => $id) {
            $this->update(['order' => $no + 1], $id);
        }

        return $orderedProductIds;
    }

    /**
     * Get order from list products
     *
     * @param $products
     * @param $request
     * @return array
     */
    public function getOrder($products, $request)
    {
        $orderedProducts = [];

        if ($request->get('naam') === "desc" ) {
            $orderedProducts = $products->sortByDesc('product_name');
        }

        if ($request->get('naam') === "asc" ) {
            $orderedProducts = $products->sortBy('product_name');
        }

        if ($request->get('prijs') === "desc" ) {
            $orderedProducts = $products->sortByDesc('price');
        }

        if ($request->get('prijs') === "asc" ) {
            $orderedProducts = $products->sortBy('price');
        }

        return $orderedProducts;
    }

    /**
     * Get workspace from list products
     *
     * @param array $productIds
     * @return \Illuminate\Support\HigherOrderCollectionProxy|int|mixed|null
     */
    public function getWorkspaceFromProduct($productIds = [])
    {
        $firstProductId = collect($productIds)->first();
        $workspaceId = null;
        if ($firstProductId) {
            $firstProduct = Product::find($firstProductId);
            if ($firstProduct) {
                $workspaceId = $firstProduct->workspace_id;
            }
        }

        return $workspaceId;
    }

    /**
     * Get min price discount by the coupon
     *
     * @param $cartId
     * @param $products
     */
    public function getMinCouponDiscountPrice($cartId, $products, $coupon)
    {
        $cart = Cart::find($cartId);
        $productPrices = Helper::calculatePriceFromCart($cart, $products);
        $discountValue = Helper::calculateCouponDiscountValue($coupon, $productPrices);

        return Helper::getProductDiscountValues($productPrices['vatProducts'], $productPrices['unitPricesProduct'], $discountValue);
    }

    /**
     * Delete product favorite
     *
     * @param $category
     */
    public function deleteFavorite($category)
    {

        $productFavorite = $category->products->pluck('id');
        $favorite = \App\Models\ProductFavorite::whereIn('product_id', $productFavorite)->delete();

        return $category;
    }

    public function connectorProductList($limit = 10, $workspaceId = null, $name = null, $updatedSince = null) {
        $products = $this->makeModel()->with(
            'workspace',
            'category',
            'options'
        );

        if(!is_null($workspaceId)) {
            $products = $products->whereIn('workspace_id', Workspace::getPrinterGroupWorkspaceIds($workspaceId));
        }

        if(!is_null($name)) {
            $products = $products->whereHas('translations', function($query) use ($name) {
                $query->where('name', $name);
            });
        }

        if(!is_null($updatedSince)) {
            $products = $products->where('updated_at', '>', $updatedSince);
        }

        return $products->paginate($limit);
    }
}
