<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCityAPIRequest;
use App\Http\Requests\API\UpdateCityAPIRequest;
use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CityController
 * @package App\Http\Controllers\API
 */
class CityAPIController extends AppBaseController
{
    /** @var  CityRepository */
    private $cityRepository;

    public function __construct(CityRepository $cityRepo)
    {
        parent::__construct();

        $this->cityRepository = $cityRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->cityRepository->pushCriteria(new RequestCriteria($request));
            $this->cityRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $cities = $this->cityRepository->paginate($limit);

        return $this->sendResponse($cities->toArray(), trans('city.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateCityAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCityAPIRequest $request)
    {
        $input = $request->all();

        $city = $this->cityRepository->create($input);

        return $this->sendResponse($city->toArray(), trans('city.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var City $city */
        $city = $this->cityRepository->findWithoutFail($id);

        if (empty($city)) {
            return $this->sendError(trans('city.not_found'));
        }

        return $this->sendResponse($city->toArray(), trans('city.message_retrieved_successfully'));
    }

    /**
     * @param UpdateCityAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCityAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var City $city */
        $city = $this->cityRepository->findWithoutFail($id);

        if (empty($city)) {
            return $this->sendError(trans('city.not_found'));
        }

        $city = $this->cityRepository->update($input, $id);

        return $this->sendResponse($city->toArray(), trans('city.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var City $city */
        $city = $this->cityRepository->findWithoutFail($id);

        if (empty($city)) {
            return $this->sendError(trans('city.not_found'));
        }

        $city->delete();

        return $this->sendResponse($id, trans('city.message_deleted_successfully'));
    }
}
