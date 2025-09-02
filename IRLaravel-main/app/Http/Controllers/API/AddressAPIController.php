<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAddressAPIRequest;
use App\Http\Requests\API\UpdateAddressAPIRequest;
use App\Http\Requests\API\UpdateAddressLocationAPIRequest;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */
class AddressAPIController extends AppBaseController
{
    /**
     * @var AddressRepository $addressRepository
     */
    private $addressRepository;

    public function __construct(AddressRepository $addressRepo)
    {
        parent::__construct();

        $this->addressRepository = $addressRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->addressRepository->pushCriteria(new RequestCriteria($request));
            $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $addresses = $this->addressRepository->paginate($limit);

        $addresses->transform(function (Address $address) {
            return $address->getFullInfo();
        });

        return $this->sendResponse($addresses->toArray(), trans('address.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateAddressAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAddressAPIRequest $request)
    {
        $input = $request->all();

        $address = $this->addressRepository->create($input);

        return $this->sendResponse($address->toArray(), trans('address.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('address.not_found'));
        }

        return $this->sendResponse($address->toArray(), trans('address.message_retrieved_successfully'));
    }

    /**
     * @param UpdateAddressAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAddressAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('address.not_found'));
        }

        $address = $this->addressRepository->update($input, $id);

        return $this->sendResponse($address->toArray(), trans('address.message_updated_successfully'));
    }

    /**
     * Update the location (latitude, longitude) of the location
     *
     * @param UpdateAddressLocationAPIRequest $request
     * @param Address $address
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function updateLocation(UpdateAddressLocationAPIRequest $request, Address $address)
    {
        $input = $request->only(['latitude', 'longitude']);

        $address = $this->addressRepository->update($input, $address->id);
        $result = $address->getFullInfo();

        return $this->sendResponse($result, trans('address.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('address.not_found'));
        }

        $address->delete();

        return $this->sendResponse($id, trans('address.message_deleted_successfully'));
    }
}
