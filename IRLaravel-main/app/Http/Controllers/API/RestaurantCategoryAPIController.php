<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRestaurantCategoryAPIRequest;
use App\Http\Requests\API\UpdateRestaurantCategoryAPIRequest;
use App\Models\RestaurantCategory;
use App\Repositories\RestaurantCategoryRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class RestaurantCategoryController
 * @package App\Http\Controllers\API
 */
class RestaurantCategoryAPIController extends AppBaseController
{
    /** @var  RestaurantCategoryRepository */
    private $restaurantCategoryRepository;

    public function __construct(RestaurantCategoryRepository $restaurantCategoryRepo)
    {
        parent::__construct();

        $this->restaurantCategoryRepository = $restaurantCategoryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->restaurantCategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->restaurantCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $restaurantCategories = $this->restaurantCategoryRepository->paginate($limit);

        return $this->sendResponse($restaurantCategories->toArray(), trans('restaurant_category.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateRestaurantCategoryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRestaurantCategoryAPIRequest $request)
    {
        $input = $request->all();

        $restaurantCategory = $this->restaurantCategoryRepository->create($input);

        return $this->sendResponse($restaurantCategory->toArray(), trans('restaurant_category.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var RestaurantCategory $restaurantCategory */
        $restaurantCategory = $this->restaurantCategoryRepository->findWithoutFail($id);

        if (empty($restaurantCategory)) {
            return $this->sendError(trans('restaurant_category.not_found'));
        }

        return $this->sendResponse($restaurantCategory->toArray(), trans('restaurant_category.message_show_successfully'));
    }

    /**
     * @param UpdateRestaurantCategoryAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRestaurantCategoryAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var RestaurantCategory $restaurantCategory */
        $restaurantCategory = $this->restaurantCategoryRepository->findWithoutFail($id);

        if (empty($restaurantCategory)) {
            return $this->sendError(trans('restaurant_category.not_found'));
        }

        $restaurantCategory = $this->restaurantCategoryRepository->update($input, $id);

        return $this->sendResponse($restaurantCategory->toArray(), trans('restaurant_category.message_update_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var RestaurantCategory $restaurantCategory */
        $restaurantCategory = $this->restaurantCategoryRepository->findWithoutFail($id);

        if (empty($restaurantCategory)) {
            return $this->sendError(trans('restaurant_category.not_found'));
        }

        $restaurantCategory->delete();

        return $this->sendResponse($id, trans('restaurant_category.message_delete_successfully'));
    }
}
