<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCountryAPIRequest;
use App\Http\Requests\API\UpdateCountryAPIRequest;
use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CountryController
 * @package App\Http\Controllers\API
 */
class CountryAPIController extends AppBaseController
{
    /** @var  CountryRepository */
    private $countryRepository;

    public function __construct(CountryRepository $countryRepo)
    {
        parent::__construct();

        $this->countryRepository = $countryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->countryRepository->pushCriteria(new RequestCriteria($request));
            $this->countryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $countries = $this->countryRepository->paginate($limit);

        return $this->sendResponse($countries->toArray(), trans('country.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateCountryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCountryAPIRequest $request)
    {
        $input = $request->all();

        $countries = $this->countryRepository->create($input);

        return $this->sendResponse($countries->toArray(), trans('country.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Country $country */
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            return $this->sendError(trans('country.not_found'));
        }

        return $this->sendResponse($country->toArray(), trans('country.message_retrieved_successfully'));
    }

    /**
     * @param UpdateCountryAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCountryAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Country $country */
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            return $this->sendError(trans('country.not_found'));
        }

        $country = $this->countryRepository->update($input, $id);

        return $this->sendResponse($country->toArray(), trans('country.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Country $country */
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            return $this->sendError(trans('country.not_found'));
        }

        $country->delete();

        return $this->sendResponse($id, trans('country.message_deleted_successfully'));
    }
}
