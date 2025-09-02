<?php

namespace App\Repositories;

use App\Facades\Helper;
use App\Jobs\SendEmail;
use App\Models\Category;
use App\Models\Order;
use App\Models\ProductFavorite;
use App\Models\SettingDeliveryConditions;
use App\Models\SettingOpenHour;
use App\Models\User;
use App\Models\Media;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class WorkspaceRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'name',
        'surname',
        'active',
        'created_at',
        'updated_at',
        'deleted_at',
        'account_manager_id',
        'gsm',
        'manager_name',
        'address',
        'btw_nr',
        'email',
        'language',
        'country_id',
        'first_login',
        'status',
        'address_lat',
        'address_long',
        'is_online',
        'is_test_mode',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Workspace::class;
    }

    /**
     * Get Shipment fulfillment options
     *
     * @return array
     */
    public function getFulfillmentOptions()
    {
        return trans('workspace.fulfillment_options');
    }

    /**
     * @param Request $request
     * @param $user
     * @param int $perPage
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getLists(Request $request, $user, $perPage = 10)
    {
        $model = $this->makeModel();
        if ($user->is_super_admin != User::SUPER_ADMIN_ID) {
            $model = $model->where(function ($query) use($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('account_manager_id', $user->id);
            });
        }

        // Search by name
        if (!empty($request->keyword_search)) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword_search. '%');
            });
        }
        
        $model = $model->with([
            'workspaceExtras', 
            'workspaceCategories', 
            'country', 
            'user', 
            'userManager', 
            'workspaceAvatar', 
            'workspaceGalleries', 
            'workspaceAccount'
        ]);

        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';
        
        if (!empty($request->sort_by) && $request->sort_by == 'types') {
            $model = $model->with(['workspaceCategories' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else if (!empty($request->sort_by) && $request->sort_by == 'manager_name') {
             $model = $model->withCount(['workspaceAccount AS manager_name' => function ($query) use ($request) {
                $query->select(\DB::raw('name'));
            }])->orderBy('manager_name', $request->order_by);
        }
        else if (!empty($request->sort_by) && $request->sort_by == 'last_order') {
            $model = $model->withCount(['orders AS last_order' => function ($query) {
                $query->select(\DB::raw('max(created_at)'));
            }])->orderBy('last_order', $request->order_by);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }
        
        $model = $model->paginate($perPage);

        return $model;
    }

    public function assignAccountManager($originManagerId, $newManagerId) {
        return $this->makeModel()
            ->where('account_manager_id', $originManagerId)
            ->update([
                'account_manager_id' => $newManagerId
            ]);
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $attributes['facebook_enabled'] = !empty($attributes['facebook_enabled']) ? 1 : 0;
        $attributes['google_enabled'] = !empty($attributes['google_enabled']) ? 1 : 0;
        $attributes['apple_enabled'] = !empty($attributes['apple_enabled']) ? 1 : 0;

        $model = parent::create($attributes);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Media::AVATAR);
        }

        return $model;
    }

    /**
     * Force delete a entity in repository by id
     *
     * @param $id
     *
     * @return bool|null
     */
    public function forceDelete($id)
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->find($id);

        return $model->forceDelete();
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateWorkspace(array $attributes, $id)
    {
        $attributes['facebook_enabled'] = !empty($attributes['facebook_enabled']) ? 1 : 0;
        $attributes['google_enabled'] = !empty($attributes['google_enabled']) ? 1 : 0;
        $attributes['apple_enabled'] = !empty($attributes['apple_enabled']) ? 1 : 0;

        $model = parent::update($attributes, $id);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Media::AVATAR,'workspaceAvatar');
        }
        
        if (array_key_exists('galleries', $attributes)) {
            $this->attachFiles($model, $attributes['galleries'], Media::GALLERIES, 'workspaceGalleries');
        }

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param \Illuminate\Http\Request|string|array $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function commonFilter($model, $request)
    {
        $arrRequest = $request->all();

        // Get order by from request
        list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

        // Search by keyword (name)
        if ($request->has('keyword') && trim((string)$request->get('keyword') . '') != '') {
            $keyword = trim((string)$request->get('keyword') . '');
            $model = $model->where('workspaces.name', 'LIKE', "%{$keyword}%");
        }

        // Filter by online status (Open/Close)
        if (array_key_exists('is_online', $arrRequest)) {
            $isOnline = filter_var($request->get('is_online'), FILTER_VALIDATE_BOOLEAN);
            $model = $model->where('workspaces.is_online', $isOnline);
        }
        // Type: Takeout / Delivery / Group
        $type = (int)$request->get('open_type', \App\Models\SettingPreference::TYPE_TAKEOUT);
        $isFilterByType = (array_key_exists('open_type', $arrRequest) && trim((string)$request->get('open_type') . '') != '');
        $typeGroup = false;

        // Special case with request open_type
        // Allow values: 0, 1, 2, 3.
        // Note: 0: Takeout; 1: Delivery; 2: Group ordering; 3: In-house
        if ($type == 2) {
            $type = \App\Models\SettingPreference::TYPE_TAKEOUT;
            $isFilterByType = false;
            $typeGroup = true;
        } else if ($type == 3) {
            $type = \App\Models\SettingPreference::TYPE_IN_HOUSE;
        }

        // Group ordering is now part of the filter under the above Order types.
        $isGroup = filter_var($request->get('is_group'), FILTER_VALIDATE_BOOLEAN);

        if ($isGroup) {
            $typeGroup = true;
        }

        // Filter by type is group is ON
        if ($typeGroup) {
            $model = $model->join('workspace_extras AS extras_group_ordering', function ($join) {
                $join->on('extras_group_ordering.workspace_id', '=', 'workspaces.id')
                    ->where(function ($query) {
                        $query->where('extras_group_ordering.type', \App\Models\WorkspaceExtra::GROUP_ORDER)
                            ->where('extras_group_ordering.active', true);
                    });
            });
        }

        $dataFilter = $request->all();
        if (isset($dataFilter['isLoyalty'])) {
            $model = $model->join('workspace_extras AS extras_loyalty', function ($join) use ($dataFilter) {
                $join->on('extras_loyalty.workspace_id', '=', 'workspaces.id')
                    ->where(function ($query) use ($dataFilter) {
                        $query->where('extras_loyalty.type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                            ->where('extras_loyalty.active', $dataFilter['isLoyalty']);
                    });
            });
        }

        // Weergeven in overkoepelende app
        if ($request->has('display_in_app')) {
            $model = $model->join('workspace_extras AS extras_display_in_app', function ($join) {
                $join->on('extras_display_in_app.workspace_id', '=', 'workspaces.id')
                    ->where(function ($query) {
                        $query->where('extras_display_in_app.type', \App\Models\WorkspaceExtra::DISPLAY_IN_APP)
                            ->where('extras_display_in_app.active', true);
                    });
            });
        }

        // Filter by setting_open_hours
        // type: '0: takeout, 1: delivery, 2: in-house'
        if ($isFilterByType) {
            $model = $model->join('setting_open_hours', 'setting_open_hours.workspace_id', '=', 'workspaces.id')
                ->where('setting_open_hours.type', $type)
                ->where('setting_open_hours.active', true);

            // Filter by open status (Open/Close)
            if (array_key_exists('is_open', $arrRequest) && trim($request->get('is_open') . '') != '') {
                $isOpen = filter_var($request->get('is_open'), FILTER_VALIDATE_BOOLEAN);

                $timezone = $request->header('Timezone', config('app.timezone'));
                $timestamp = \Carbon\Carbon::now()->timezone($timezone);
                $strTimestamp = $timestamp->toDateTimeString();
                $dayNumber = $timestamp->dayOfWeek;
                $openTimeslotsClass = addslashes(\App\Models\SettingOpenHour::class);
                $sqlOpenTimeslots = "(SELECT COUNT(open_timeslots.id) FROM open_timeslots 
                    WHERE open_timeslots.workspace_id = setting_open_hours.workspace_id 
                        AND open_timeslots.foreign_id = setting_open_hours.id
                        AND open_timeslots.foreign_model = '{$openTimeslotsClass}' 
                        AND open_timeslots.status = 1 
                        AND open_timeslots.day_number = ? 
                        AND (open_timeslots.start_time <= ? AND open_timeslots.end_time >= ?) 
                    )";

                if ($isOpen) {
                    $sqlOpenTimeslots .= " > 0";
                } else {
                    $sqlOpenTimeslots .= " = 0";
                }

                $model = $model->whereRaw($sqlOpenTimeslots, [$dayNumber, $strTimestamp, $strTimestamp]);
            }
        }

        // Filter by category
        if ($request->has('restaurant_category_id')) {
            $model = $model->join('workspace_category', 'workspace_category.workspace_id', '=', 'workspaces.id')
                ->where('workspace_category.restaurant_category_id', (int)$request->get('restaurant_category_id'))
                ->groupBy('workspaces.id');
        }

        // Filter by location: lat, long, radius
        if ($request->has('lat') || $request->has('lng')) {
            $latitude = $this->escapeLocation($request->get('lat', config('location.default.lat')));
            $longitude = $this->escapeLocation($request->get('lng', config('location.default.lng')));
            $radius = (int)$request->get('radius', config('location.default.radius'));

            /**
             * Laravel Find Nearest Restaurants from certain GPS Latitude and Longitude
             * @link https://www.techalyst.com/posts/laravel-find-nearest-restaurants-from-certain-gps-latitude-and-longitude
             */
            $sqlDistance = "( 6371000 * acos( cos( radians({$latitude}) ) * cos( radians( address_lat ) )
                   * cos( radians( address_long ) - radians({$longitude}) ) + sin( radians({$latitude}) ) 
                   * sin( radians( address_lat ) ) ) )";
            $model = $model->addSelect(\DB::raw("{$sqlDistance} AS distance"))
                ->where(\DB::raw("{$sqlDistance}"), '<', $radius);

            if (empty($orderBy)) {
                // Default order by distance
                $model = $model->orderBy("distance", (!empty($orderBy) && $orderBy == 'distance') ? $sortBy : 'asc');
            }

            // Only apply with type is "delivery"
            // Always filter by conditions in /manager/settings/delivery-conditions
            if ($type == \App\Models\SettingPreference::TYPE_DELIVERY) {
                // unit: m. 1km = 1000m
                $model = $model->join('setting_delivery_conditions', 'setting_delivery_conditions.workspace_id', '=', 'workspaces.id')
                    ->addSelect('area_start', 'area_end', 'price', 'price_min')
                    ->where('area_start', '<=', \DB::raw("({$sqlDistance} / 1000)"))
                    ->where('area_end', '>=', \DB::raw("({$sqlDistance} / 1000)"));
                if (isset($dataFilter['minimumOrderAmount']) || isset($dataFilter['deliveryCharge'])) {
                    if (!empty($dataFilter['minimumOrderAmount'])) {
                        $model = $model->where('price_min', '<=', $dataFilter['minimumOrderAmount']);
                    }
                    if (isset($dataFilter['deliveryCharge']) && $dataFilter['deliveryCharge'] != '') {
                        $model = $model->where('price', '<=', $dataFilter['deliveryCharge']);
                    }
                }

                // Delivery order by
                if (!empty($orderBy)) {
                    $sortBy = (!empty($sortBy)) ? $sortBy : 'asc';
                    $orderByColumn = $orderBy;

                    // Order by in setting_delivery_conditions table
                    // By: minimum bedrag, leveringskost
                    if (in_array($orderBy, ['amount', 'delivery_fee'])) {
                        // Order by column
                        if ($orderBy == 'amount') {
                            $orderByColumn = 'price_min';
                        } else if ($orderBy == 'delivery_fee') {
                            $orderByColumn = 'price';
                        }

                        $model = $model->orderBy($orderByColumn, $sortBy);
                    }
                }
            }
        }

        // Filter by active status
        if ($request->has('active') && $request->get('active') != '') {
            $isActive = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
            $model = $model->where('workspaces.active', $isActive);
        }

        // Filter when has coupon
        if (!empty($request->get('has_coupon'))) {
            $model->whereRaw("(SELECT COUNT(coupons.id)
                FROM coupons
                WHERE coupons.expire_time > NOW() 
                    AND coupons.active = 1 
                    AND coupons.workspace_id = workspaces.id 
                    AND coupons.deleted_at IS NULL
            ) > 0");
        }

        // General order by
        if (!empty($orderBy)) {
            $sortBy = (!empty($sortBy)) ? $sortBy : 'asc';

            // By: naam
            if (in_array($orderBy, ['name', 'distance'])) {
                // Order by main table
                $model = $model->orderBy($orderBy, $sortBy);
            }
            // Order by in setting_preferences table
            // By: minimum wachttijd
            else if (in_array($orderBy, ['waiting_time'])) {
                $model = $model->leftJoin('setting_preferences', 'setting_preferences.workspace_id', '=', 'workspaces.id');

                if ($type == \App\Models\SettingPreference::TYPE_TAKEOUT) {
                    // When type is takeout
                    $defaultMinTime = (int)config('settings.preferences.takeout_min_time');
                    $model = $model->addSelect(\DB::raw("IFNULL(takeout_min_time, {$defaultMinTime}) AS takeout_min_time"))
                        ->orderBy('takeout_min_time', $sortBy);
                } else if ($type == \App\Models\SettingPreference::TYPE_DELIVERY) {
                    // When type is delivery
                    $defaultMinTime = (int)config('settings.preferences.delivery_min_time');
                    $model = $model->addSelect(\DB::raw("IFNULL(delivery_min_time, {$defaultMinTime}) AS delivery_min_time"))
                        ->orderBy('delivery_min_time', $sortBy);
                }
            }
        }

        return $model;
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
            $model = $model->select('workspaces.*')
                ->with([
                    'user',
                    'country',
                    'workspaceAvatar',
                    'workspaceGalleries',
                    'settingPreference',
                    'settingDeliveryConditions',
                    'workspaceExtras',
                    'settingOpenHours',
                    'workspaceCategories',
                ]);

            // Run common filters
            $model = $this->commonFilter($model, $request);

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * list of all the restaurants in the system where the user has ordered in the past.
     *
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function ordered($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();
        $user = $this->getJWTAuth();

        // Filter
        $this->scopeQuery(function ($model) use ($request, $arrRequest, $user) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            // Prevent duplicate field
            $model = $model->select('workspaces.*')
                ->with([
                    'user',
                    'country',
                    'workspaceAvatar',
                    'workspaceGalleries',
                    'settingPreference',
                    'settingDeliveryConditions',
                    'workspaceExtras',
                    'settingOpenHours',
                    'workspaceCategories',
                ]);

            // Run common filters
            $model = $this->commonFilter($model, $request);

            // Only allow get list when user is authorized
            if (!empty($user)) {
                // Join with orders table
                $model = $model->leftJoin('orders', 'orders.workspace_id', '=', 'workspaces.id');
                // Only get workspace which have any order
                $model = $model->whereNotNull('orders.id')
                    ->where('orders.user_id', $user->id)
                    ->groupBy('orders.workspace_id')
                    ->orderBy('orders.id', 'desc');
            } else {
                $model = $model->where('id', 0);
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * list of all the restaurants in the system where the user liked at least one of the restaurant products.
     *
     * @param int|null $limit
     * @param string[] $columns
     * @param string $method
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function liked($limit = null, $columns = ['*'], $method = "paginate")
    {
        $request = request();
        $arrRequest = $request->all();
        $user = $this->getJWTAuth();

        // Filter
        $this->scopeQuery(function ($model) use ($request, $arrRequest, $user) {
            /** @var \Illuminate\Database\Eloquent\Model $model */
            // Prevent duplicate field
            $model = $model->select('workspaces.*')
                ->with([
                    'user',
                    'country',
                    'workspaceAvatar',
                    'workspaceGalleries',
                    'settingPreference',
                    'settingDeliveryConditions',
                    'workspaceExtras',
                    'settingOpenHours',
                    'workspaceCategories',
                ]);

            // Run common filters
            $model = $this->commonFilter($model, $request);

            // Only allow get list when user is authorized
            if (!empty($user)) {
                // Join with products table
                $model = $model
                    ->leftJoin('products', 'products.workspace_id', '=', 'workspaces.id')
                    ->leftJoin('product_favorites', 'product_favorites.product_id', '=', 'products.id');
                // Only get workspace which have any like
                $model = $model->whereNotNull('product_favorites.product_id')
                    ->where('product_favorites.user_id', $user->id)
                    ->groupBy('workspaces.id')
                    ->orderBy('product_favorites.id', 'desc');
            } else {
                $model = $model->where('id', 0);
            }

            return $model;
        });

        return parent::paginate($limit, $columns, $method);
    }

    /**
     * Get latest orders of a user in the workspaces
     *
     * @param User $user
     * @param array $workspaceIds
     * @return array
     */
    public function latestOrdered(User $user, array $workspaceIds)
    {
        // When invalid
        if (empty($user) || empty($workspaceIds)) {
            return [];
        }

        // Query by user and workspace list
        $latestOrders = Order::whereIn('id', function ($query) use ($user, $workspaceIds) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->select(\DB::raw('MAX(id)'))
                ->from('orders')
                ->where('user_id', $user->id)
                ->whereIn('workspace_id', $workspaceIds)
                ->groupBy('workspace_id');
        })
            ->with([
                'workspace',
                'user',
                'orderItems',
                'orderItems.product',
                'orderItems.product.translations',
                'orderItems.product.workspace',
                'orderItems.product.productAvatar',
                'orderItems.product.category',
                'orderItems.product.category.translations',
                'orderItems.product.productLabels',
                'orderItems.product.productAllergenens',
                'orderItems.product.productFavorites',
                'orderItems.product.options',
                'orderItems.product.vat',
                'orderItems.product.productLabels' => function ($query) {
                    // Only get active labels
                    $query->where('active', true);
                },
                'orderItems.optionItems',
                'orderItems.optionItems.option',
                'orderItems.optionItems.optionItem',
                'orderItems.optionItems.optionItem.option',
                'orderItems.optionItems.optionItem.option.translations',
                'orderItems.optionItems.optionItem.option.workspace',
            ])
            ->get();
        $data = [];

        /** @var \App\Models\Order $order */
        foreach ($latestOrders as $order) {
            $data[$order->workspace_id] = $order->getFullInfo();
        }

        return $data;
    }

    /**
     * Get latest orders of a user in the workspaces
     *
     * @param User $user
     * @param array $workspaceIds
     * @return array
     */
    public function countFavoriteProducts(User $user, array $workspaceIds)
    {
        // When invalid
        if (empty($user) || empty($workspaceIds)) {
            return [];
        }

        // Query by user and workspace list
        $favoriteProducts = ProductFavorite::select('products.workspace_id')
            ->addSelect(\DB::raw('COUNT(product_favorites.product_id) AS products_count'))
            ->join('products', 'products.id', '=', 'product_favorites.product_id')
            ->where('product_favorites.user_id', $user->id)
            ->whereIn('products.workspace_id', $workspaceIds)
            ->groupBy('products.workspace_id')
            ->get();
        $data = [];

        /** @var \App\Models\Order $item */
        foreach ($favoriteProducts as $item) {
            $data[$item->workspace_id] = $item->getAttribute('products_count');
        }

        return $data;
    }

    /**
     * Get latest orders of a user in the workspaces
     *
     * @param string $field
     * @param array $workspaceIds
     * @return array
     */
    public function checkCategoryFavorite(string $field, array $workspaceIds)
    {
        // When invalid
        if ((empty($field) || !in_array($field, ['favoriet_friet', 'kokette_kroket'])) || empty($workspaceIds)) {
            return [];
        }

        $aliasCount = 'favorite_count';
        // Query by user and workspace list
        $categoryFavorite = Category::select('categories.workspace_id')
            ->addSelect(\DB::raw("COUNT(categories.id) AS {$aliasCount}"))
            ->where("categories.{$field}", true)
            ->whereIn('categories.workspace_id', $workspaceIds)
            ->groupBy('categories.workspace_id')
            ->get();
        $data = [];

        /** @var \App\Models\Order $item */
        foreach ($categoryFavorite as $item) {
            $data[$item->workspace_id] = $item->getAttribute($aliasCount) > 0;
        }

        return $data;
    }

    /**
     * Check open hours of the restaurants by open type
     *
     * @param array $workspaceIds
     * @param int|null $openType Restaurant open type. Please check in setting_open_hours.type
     * @param string|null $timezone Timezone string. Eg: UTC, Asia/Ho_Chi_Minh,...
     * @return array
     */
    public function checkOpenHours(array $workspaceIds, $openType = null, $timezone = null)
    {
        $timestamp = \Carbon\Carbon::now($timezone);
        $dayOfWeek = $timestamp->dayOfWeek;
        $openHours = \App\Models\OpenTimeslot::join('setting_open_hours', function ($join) {
                $join->on('setting_open_hours.id', '=', 'open_timeslots.foreign_id')
                    ->where('open_timeslots.foreign_model', \App\Models\SettingOpenHour::class);
            })
            ->whereIn('setting_open_hours.workspace_id', $workspaceIds)
            ->where('setting_open_hours.active', true)
            ->where(function ($query) use ($timestamp, $dayOfWeek) {
                $query->where('open_timeslots.start_time', '<=', $timestamp)
                    ->where('open_timeslots.end_time', '>=', $timestamp);
            })
            ->where('open_timeslots.day_number', $dayOfWeek)
            ->where('open_timeslots.status', true);

        // Filter by setting_open_hours.type
        if ($openType !== null) {
            $openHours->where('setting_open_hours.type', $openType);
        }

        $openHours = $openHours->pluck('open_timeslots.id', 'open_timeslots.workspace_id')->toArray();
        $result = [];

        foreach ($workspaceIds as $workspaceId) {
            $result[$workspaceId] = array_key_exists($workspaceId, $openHours);
        }

        return $result;
    }

    /**
     * @param $workspaceIds
     * @param $managerId
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function assignAccountManagerToWorkspaces($workspaceIds, $managerId) {
        return $this->makeModel()
            ->whereIn('id', $workspaceIds)
            ->update([
                'account_manager_id' => $managerId
            ]);
    }

    /**
     * @param $user
     */
    public function sendInvitation($user) {
        $newPassword = Str::random(6);
        $user->password = Hash::make($newPassword);
        $user->default_password = $newPassword;
        $user->setRememberToken(Str::random(60));
        $user->status = User::STATUS_ACTIVE;
        $user->is_verified = true;
        $user->verify_expired_at = null;
        $user->save();

        dispatch(new SendEmail([
            'template' => 'emails.workspace_invitation',
            'user' => $user,
            'link' => route('manager.showlogin'),
            'newPassword' => $newPassword,
            'subject' => trans('workspace.send_invitation_subject'),
        ], $user->getLocale()));
    }

    /**
     * Get delivery condition settings from a restaurant
     *
     * @param int $workspaceId
     * @param array $location
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getDeliveryConditions(int $workspaceId, array $location)
    {
        $qWorkspace = Workspace::select('workspaces.*')
            ->with(['user', 'country', 'workspaceAvatar'])
            ->where('workspaces.active', true)
            ->where('workspaces.id', $workspaceId);

        // Filter by location: lat, long. Unit: m.
        $latitude = $this->escapeLocation(array_get($location, 'lat', 0));
        $longitude = $this->escapeLocation(array_get($location, 'lng', 0));

        /**
         * Laravel Find Nearest Restaurants from certain GPS Latitude and Longitude
         * @link https://www.techalyst.com/posts/laravel-find-nearest-restaurants-from-certain-gps-latitude-and-longitude
         */
        $sqlDistance = "( 6371000 * acos( cos( radians({$latitude}) ) * cos( radians( address_lat ) )
                   * cos( radians( address_long ) - radians({$longitude}) ) + sin( radians({$latitude}) ) 
                   * sin( radians( address_lat ) ) ) )";

        // Get workspace detail
        /** @var \App\Models\Workspace $workspace */
        $workspace = $qWorkspace
            ->addSelect(\DB::raw("{$sqlDistance} AS distance"))
            ->first();

        // When invalid
        if (empty($workspace)) {
            throw new \Exception('Not found', 404);
        }

        $distance = (int)$workspace->getDistanceFormat($workspace->distance);
        // unit: m. 1km = 1000m
        $distance = $distance / 1000;

        $conditions = \App\Models\SettingDeliveryConditions::where('workspace_id', $workspaceId)
            ->where(function ($query) use ($distance) {
                $query
                    ->where('area_start', '<=', $distance)
                    ->where('area_end', '>=', $distance);
            })
            ->get();

        return $conditions;
    }

    /**
     * Get min delivery condition
     *
     * @param int $workspaceId
     * @return array
     */
    public function getMinDeliveryCondition(int $workspaceId)
    {
        $result = [];

        // Leverkost
        /** @var \App\Models\SettingDeliveryConditions $settings */
        $settings = \App\Models\SettingDeliveryConditions::where('workspace_id', $workspaceId)
            ->orderBy('price', 'ASC')
            ->first(['price']);
        $result['price'] = (!empty($settings)) ? Helper::formatCurrencyNumber($settings->price) : 0;

        // Levering minimum
        /** @var \App\Models\SettingDeliveryConditions $condition */
        $settings = \App\Models\SettingDeliveryConditions::where('workspace_id', $workspaceId)
            ->orderBy('price_min', 'ASC')
            ->first(['price_min']);
        $result['price_min'] = (!empty($settings)) ? Helper::formatCurrencyNumber($settings->price_min) : 0;

        // free
        /** @var \App\Models\SettingDeliveryConditions $condition */
        $settings = \App\Models\SettingDeliveryConditions::where('workspace_id', $workspaceId)
            ->orderBy('free', 'ASC')
            ->first(['free']);
        $result['free'] = (!empty($settings)) ? Helper::formatCurrencyNumber($settings->free) : 0;

        /** @var \App\Models\SettingPreference $condition */
        $settings = \App\Models\SettingPreference::where('workspace_id', $workspaceId)
            ->first(['delivery_min_time']);
        $result['delivery_min_time'] = (!empty($settings)) ? $settings->delivery_min_time : 0;

        return $result;
    }

    /**
     * Get the workspace record by the token
     *
     * @param string $token
     * @return \App\Models\Workspace
     * @throws \Exception
     */
    public function getByToken(string $token)
    {
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = Workspace::whereToken($token)->active()->first();

        if (empty($workspace)) {
            throw new \Exception('Not found token "' . $token . '"', 404);
        }

        return $workspace;
    }

    /**
     * Get the workspace record by the token
     *
     * @param string $token
     * @return \App\Models\Workspace
     * @throws \Exception
     */
    public function getAppSettings(string $token)
    {
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = Workspace::whereToken($token)->active()
            ->with(['workspaceApp', 'workspaceApp.meta' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->orderBy('order', 'ASC');
            }])
            ->first();

        if (empty($workspace)) {
            throw new \Exception('Invalid token ' . $token, 404);
        }

        $workspaceApp = $workspace->workspaceApp;

        return $workspaceApp->getFullInfo();
    }

    /**
     * Get the workspace record by the token
     *
     * @param string $token
     * @return \App\Models\Workspace
     * @throws \Exception
     */
    public function getAppSettingsById($id)
    {
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = Workspace::whereId($id)->active()
            ->with(['workspaceApp', 'workspaceApp.meta' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->orderBy('order', 'ASC');
            }])
            ->first();

        if (empty($workspace)) {
            throw new \Exception('Invalid id ' . $id, 404);
        }

        $workspaceApp = $workspace->workspaceApp;

        return $workspaceApp->getFullInfo();
    }

    public function getRestaurantsByDistance(array $location = [], $radius = 40, $dataFilter = [], $limit = NULL)
    {
        if (empty($location)) {
            return [];
        }

        $this->scopeQuery(function ($model) use ($location, $radius, $dataFilter, $limit) {
            $model = $model->select('workspaces.*')
                ->with(['user', 'country', 'workspaceAvatar', 'workspaceCategories'])
                ->where('workspaces.active', true);

            // Filter by location: lat, long. Unit: m.
            if (!empty($radius)) {
                $latitude = $this->escapeLocation(array_get($location, 'lat', 0));
                $longitude = $this->escapeLocation(array_get($location, 'lng', 0));
                /**
                 * Laravel Find Nearest Restaurants from certain GPS Latitude and Longitude
                 * @link https://www.techalyst.com/posts/laravel-find-nearest-restaurants-from-certain-gps-latitude-and-longitude
                 */
                $sqlDistance = "( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( address_lat ) )
                   * cos( radians( address_long ) - radians({$longitude}) ) + sin( radians({$latitude}) ) 
                   * sin( radians( address_lat ) ) ) )";

                // Get workspace detail
                /** @var \App\Models\Workspace $workspace */
                $model = $model
                    ->addSelect(\DB::raw("{$sqlDistance} AS distance"))
                    ->groupBy('distance')
                    ->having("distance", "<", $radius);
            }

            $restaurantTypes = [
                SettingOpenHour::TYPE_TAKEOUT => SettingOpenHour::TAKEOUT,
                SettingOpenHour::TYPE_DELIVERY => SettingOpenHour::LEVERING,
            ];

            $typeGroup = false;
            $dataType = SettingOpenHour::TYPE_TAKEOUT;
            if (isset($dataFilter['choose_type'])) {
                if ($dataFilter['choose_type'] == SettingOpenHour::GROUP) {
                    $dataType = false;
                    $typeGroup = true;
                } else {
                    $dataType = array_search($dataFilter['choose_type'], $restaurantTypes);
                }
            }

            if ($dataType !== false) {
                $model = $model->join('setting_open_hours', 'setting_open_hours.workspace_id', '=', 'workspaces.id')
                    ->where('setting_open_hours.type', $dataType)
                    ->where('setting_open_hours.active', true);
            }

            if (isset($dataFilter['restaurantName']) && !empty($dataFilter['restaurantName'])) {
                $model = $model->where('name', 'LIKE', '%' . $dataFilter['restaurantName']. '%');
            }

            if (isset($dataFilter['categoryId']) && !empty($dataFilter['categoryId'])) {
                $model = $model->join('workspace_category', function ($join) use ($dataFilter) {
                    $join->on('workspace_category.workspace_id', '=', 'workspaces.id')
                        ->where(function ($query) use ($dataFilter) {
                            $query->where('workspace_category.restaurant_category_id', $dataFilter['categoryId']);
                        });
                });
            }

            if (isset($dataFilter['isLoyalty'])) {
                $model = $model->join('workspace_extras AS extras_loyalty', function ($join) use ($dataFilter) {
                    $join->on('extras_loyalty.workspace_id', '=', 'workspaces.id')
                        ->where(function ($query) use ($dataFilter) {
                            $query->where('extras_loyalty.type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                                ->where('extras_loyalty.active', $dataFilter['isLoyalty']);
                        });
                });
            }
            
            if ($typeGroup) {
                $model = $model->join('workspace_extras AS extras_group_ordering', function ($join) {
                    $join->on('extras_group_ordering.workspace_id', '=', 'workspaces.id')
                        ->where(function ($query) {
                            $query->where('extras_group_ordering.type', \App\Models\WorkspaceExtra::GROUP_ORDER)
                                ->where('extras_group_ordering.active', true);
                        });
                });
            }

            //Filter by Mininum order amount and delivery charge
            if (isset($dataFilter['minimumOrderAmount']) || isset($dataFilter['deliveryCharge'])) {
                $model = $model->join('setting_delivery_conditions AS delivery_condition', function ($join) use ($dataFilter) {
                    $join->on('delivery_condition.workspace_id', '=', 'workspaces.id')
                        ->where(function ($query) use ($dataFilter) {
                            $qr = $query;
                            if(!empty($dataFilter['minimumOrderAmount'])){
                                $qr = $qr->where('delivery_condition.price_min', '<=', $dataFilter['minimumOrderAmount']);
                            }
                            if($dataFilter['deliveryCharge'] != ''){
                                $qr = $qr->where('delivery_condition.price', '<=', $dataFilter['deliveryCharge']);
                            }
                            return $qr;
                        });
                });
            }

            $model = $this->handleSorting($model, $dataFilter);

            if (!empty($limit)) {
                $model = $model->limit($limit);
            }

            return $model;
        });

        return $this->all();
    }

    /**
     * Hanle sorting when filter in portal website
     *
     * @param $model
     * @param array $dataFilter
     * @return mixed
     */
    public function handleSorting($model, $dataFilter = [])
    {
        $orderType = Workspace::DISTANCE;
        if (isset($dataFilter['orderType'])) {
            $orderType = $dataFilter['orderType'];
        }

        switch ($orderType) {
            case Workspace::DISTANCE:
            case Workspace::NAME:
                $model = $model->orderBy($orderType, 'asc');
                break;
            case Workspace::MINIMUM_AMOUNT:
            case Workspace::DELIVERY_COST:
                $deliveryQuery = SettingDeliveryConditions::select('workspace_id')
                ->selectRaw('min( setting_delivery_conditions.price_min) as minimum_amount, min( setting_delivery_conditions.price ) AS delivery_cost')
                ->groupBy('setting_delivery_conditions.workspace_id')->toSql();

                $model = $model->join(\DB::raw('(' . $deliveryQuery . ') as delivery_information'), function ($join){
                    $join->on('workspaces.id', '=', 'delivery_information.workspace_id');
                });

                if ($orderType == Workspace::MINIMUM_AMOUNT) {
                    $model = $model->orderBy('delivery_information.minimum_amount', 'asc');
                } else {
                    $model = $model->orderBy('delivery_information.delivery_cost', 'asc');
                }

                break;
            case Workspace::MINIMUM_WAITING_TIME:
                if (isset($dataFilter['choose_type'])) {
                    $model = $model->join('setting_preferences', function ($join) {
                        $join->on('setting_preferences.workspace_id', '=', 'workspaces.id');
                    });

                    if ($dataFilter['choose_type'] == SettingOpenHour::TAKEOUT) {
                        $model = $model->orderBy('setting_preferences.takeout_min_time', 'asc');
                    } else {
                        $model = $model->orderBy('setting_preferences.delivery_min_time', 'asc');
                    }
                }
                break;
            case Workspace::CREATED_AT:
                $model = $model->orderBy('workspaces.id', 'desc');
                break;
        }

        return $model;
    }

    /**
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function uploadGalleries($attributes, $id, $galleryType)
    {
        $model = parent::update($attributes, $id);

        if (array_key_exists($galleryType, $attributes)) {
            $collection = $this->attachFiles($model, $attributes[$galleryType], $galleryType);
            $this->updateUploadGalleryOrder($model, $galleryType, $collection);
            return $collection;
        }

        return false;
    }

    /**
     * Update order for gallery when uploading image
     *
     * @param $model
     * @param $galleryType
     * @return false
     */
    public function updateUploadGalleryOrder($model, $galleryType, $uploadCollection)
    {
        try {
            \DB::beginTransaction();
            $workspaceGalleries = $model->workspaceGalleries;
            if ($galleryType == Media::API_GALLERIES) {
                $workspaceGalleries = $model->workspaceAPIGalleries;
            }
            $orderMax = $workspaceGalleries->max('order');
            $uploadGalleries = [];
            foreach ($uploadCollection as $upload) {
                $uploadGalleries[$upload->id] = $upload;
            }

            $order = 1;
            foreach ($workspaceGalleries as $workspaceGallery) {
                if (array_key_exists($workspaceGallery->id, $uploadGalleries)) {
                    continue;
                }

                if (empty($orderMax)) {
                    $order = $order + count($uploadGalleries);
                } else {
                    $order = $workspaceGallery->order + count($uploadGalleries);
                }

                $workspaceGallery->update(['order' => $order]);
            }

            $order = count($uploadGalleries);
            foreach ($uploadGalleries as $uploadGallery) {
                $uploadGallery->update(['order' => $order]);
                $order--;
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return false;
        }
    }

    public function getByDomain(string $domain)
    {
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = Workspace::whereSlug($domain)->active()->first();

        if (empty($workspace)) {
            throw new \Exception('Not found domain "' . $domain . '"', 404);
        }

        return $workspace;
    }

    public function validateOrderAccessKey($id, $key)
    {
        if(empty($key)) {
            return false;
        }

        $workspace = $this->makeModel()
            ->where('id', $id)
            ->where('order_access_key', $key)
            ->first();

        return !empty($workspace);
    }
}
