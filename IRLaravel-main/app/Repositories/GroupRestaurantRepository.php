<?php
namespace App\Repositories;

use App\Models\GroupRestaurant;
use App\Models\Media;
use Illuminate\Http\Request;

class GroupRestaurantRepository extends AppBaseRepository
{
    public function model()
    {
        return GroupRestaurant::class;
    }

    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Create group restaurant
     *
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $model = parent::create($attributes);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Media::AVATAR);
        }

        return $model;
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
           'groupRestaurantWorkspaces'
        ]);

        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        if (!empty($request->sort_by) && $request->sort_by == 'restaurants') {
            $model = $model->with(['groupRestaurantWorkspaces' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }

        $model = $model->paginate($perPage);

        return $model;
    }

    /**
     * Update group restaurant
     *
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateGroupRestaurant(array $attributes, $id)
    {
        $model = parent::update($attributes, $id);

        if (array_key_exists('files', $attributes)) {
            $this->attachFiles($model, $attributes['files'], Media::AVATAR, 'groupRestaurantAvatar');
        }

        return $model;
    }

    /**
     * Get group restaurant by token
     *
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public function getByToken($token)
    {
        $groupRestaurant = GroupRestaurant::whereToken($token)->active()->first();

        if (empty($groupRestaurant)) {
            throw new \Exception('Not found group restaurant with this token!', 404);
        }

        return $groupRestaurant;
    }

    /**
     * Get list of restaurant in a group
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function getRestaurantList($id, Request $request)
    {
        /** @var \App\Models\GroupRestaurant|null $groupRestaurant */
        $groupRestaurant = GroupRestaurant::find($id);

        if (empty($groupRestaurant) || ($groupRestaurant->active != 1)) {
            throw new \Exception('Not found group restaurant with this id!', 404);
        }

        $model = $groupRestaurant->groupRestaurantWorkspaces()
            ->with([
                'user',
                'country',
                'workspaceAvatar',
                'workspaceGalleries',
                'settingPreference',
                'settingDeliveryConditions',
                'workspaceExtras',
                'settingOpenHours',
                'settingGeneral',
                'workspaceCategories'
            ]);

        // Get order by from request
        list($orderBy, $sortBy) = $this->getOrderBy($model, $request);

        // Type: Takeout / Delivery / Group
        $type = (int)$request->get('open_type', \App\Models\SettingPreference::TYPE_TAKEOUT);

        $lat = $request->get('lat');
        if (!$request->has('lat') && $request->hasHeader('lat')) {
            $lat = $request->header('lat');
        }

        $lng = $request->get('lng');
        if (!$request->has('lng') && $request->hasHeader('lng')) {
            $lng = $request->header('lng');
        }

        // Filter by location: lat, long, radius
        if (!empty($lat) || !empty($lng)) {
            $lat = ($lat !== null) ? $lat : config('location.default.lat');
            $lng = ($lng !== null) ? $lng : config('location.default.lng');

            $latitude = $this->escapeLocation($lat);
            $longitude = $this->escapeLocation($lng);
            $radius = (int)$request->get('radius', 1000000 * 1000); // Unit: m

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

        return $model->paginate();
    }
}