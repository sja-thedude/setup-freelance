<?php
namespace App\Http\Controllers\API;

use App\Repositories\GroupRestaurantRepository;
use Illuminate\Http\Request;

class GroupRestaurantAPIController extends AppBaseController
{
    protected $groupRestaurantRepository;

    public function __construct(
        GroupRestaurantRepository $groupRestaurantRepository
    ){
        parent::__construct();

        $this->groupRestaurantRepository = $groupRestaurantRepository;
    }

    public function getByToken($token, Request $request)
    {
        try {
            $groupRestaurant = $this->groupRestaurantRepository->getByToken($token);
            $result = $groupRestaurant->getFullInfo($request);
            return $this->sendResponse($result, trans('grouprestaurant.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    /**
     * Get restaurants from a restaurant group
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRestaurantList($id, Request $request)
    {
        try {
            $groupRestaurant = $this->groupRestaurantRepository->getRestaurantList($id, $request);

            $groupRestaurant->transform(function ($workspace) {
                /** @var \App\Models\Workspace $workspace */
                // Get full information
                $data = $workspace->getFullInfo();

                return $data;
            });

            $result = $groupRestaurant->toArray();

            // Customize result
            $result['restaurants'] = $result['data'];
            unset($result['data']);

            return $this->sendResponse($result, trans('grouprestaurant.restaurant_list_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }
}