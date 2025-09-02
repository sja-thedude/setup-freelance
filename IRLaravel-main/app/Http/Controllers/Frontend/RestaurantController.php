<?php
namespace App\Http\Controllers\Frontend;

use App\Models\SettingOpenHour;
use App\Models\Workspace;
use App\Repositories\RestaurantCategoryRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;

class RestaurantController extends BaseController
{
    private $restaurantCategoryRepository;
    private $workspaceRepository;

    public function __construct(
        RestaurantCategoryRepository $restaurantCategoryRepository,
        WorkspaceRepository $workspaceRepository
    ) {
        $this->restaurantCategoryRepository = $restaurantCategoryRepository;
        $this->workspaceRepository = $workspaceRepository;
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $data['isHome'] = false;
        $data['headerTitle'] = trans('dashboard.find_merchants');
        $data['isInSearchPage'] = true;
        $data['currentType'] = SettingOpenHour::TAKEOUT;
        if (isset($data['choose_type'])) {
            $data['currentType'] = $data['choose_type'];
        }

        if (isset($data['lat']) && isset($data['long'])) {
            $location = [
                'lat' => $data['lat'],
                'lng' => $data['long']
            ];
        } else {
            $location = config('location.default_search');
            $data['lat'] = $location['lat'];
            $data['long'] = $location['lng'];
        }

        $listRestaurantCategory = $this->restaurantCategoryRepository->all();
        $workspaces = $this->workspaceRepository->getRestaurantsByDistance($location, Workspace::MINIMUM_DISTANCE, $data);
        $data['listRestaurantCategory'] = $listRestaurantCategory;
        $data['workspaces'] = $workspaces;
        $data['workspaceIdList'] = implode(',', $workspaces->pluck('id')->toArray());

        if ($request->ajax()) {
            $resultHtml = view($this->guard . '.restaurants.partials.restaurants', $data)->render();

            return response()->json([
                'status' => 200,
                'resultHtml' => $resultHtml
            ]);
        }

        return view($this->guard . '.restaurants.search-restaurant', $data);
    }

    public function markerDetail(Request $request)
    {
        $workspaceIdList = $request->get('workspaceIdList');
        if ($workspaceIdList) {
            $workspaceIdArr = explode(',', $workspaceIdList);
            $workspaces = $this->workspaceRepository->findWhereIn('id', $workspaceIdArr);
            if ($workspaces->count() > 0) {
                $markers = [];
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https://' : 'http://';
                $domain = parse_url(config('app.url'), PHP_URL_HOST);
                foreach ($workspaces as $workspace) {
                    $markers[$workspace->id]['latitude'] = $workspace->address_lat;
                    $markers[$workspace->id]['longtitude'] = $workspace->address_long;
                    $subDomain = $protocol . $workspace->slug . '.' . $domain;
                    $markers[$workspace->id]['markerHtml'] = view($this->guard . '.restaurants.partials.restaurant_marker', ['workspace' => $workspace, 'subDomain' => $subDomain])->render();
                }

                return $this->sendResponse(['markers' => $markers], trans('workspace.successfully'));
            }
        }

        return $this->sendError(trans('workspace.cannot_find_workspace'));
    }
}