<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatAPIRequest;
use App\Http\Requests\API\UpdateVatAPIRequest;
use App\Models\Vat;
use App\Repositories\VatRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class VatController
 * @package App\Http\Controllers\API
 */
class VatAPIController extends AppBaseController
{
    /**
     * @var VatRepository $vatRepository
     */
    protected $vatRepository;

    /**
     * VatAPIController constructor.
     * @param VatRepository $vatRepo
     */
    public function __construct(VatRepository $vatRepo)
    {
        parent::__construct();

        $this->vatRepository = $vatRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->vatRepository->pushCriteria(new RequestCriteria($request));
            $this->vatRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $vats = $this->vatRepository->paginate($limit);

        $vats->transform(function ($item) {
            /** @var \App\Models\Vat $item */
            return $item->getFullInfo();
        });
        $result = $vats->toArray();

        return $this->sendResponse($result, trans('vat.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateVatAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateVatAPIRequest $request)
    {
        $input = $request->all();

        $vat = $this->vatRepository->create($input);

        return $this->sendResponse($vat->toArray(), trans('vat.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Vat $vat */
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            return $this->sendError(trans('vat.not_found'));
        }

        $result = $vat->getFullInfo();

        return $this->sendResponse($result, trans('vat.message_retrieved_successfully'));
    }

    /**
     * @param UpdateVatAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateVatAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Vat $vat */
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            return $this->sendError(trans('vat.not_found'));
        }

        $vat = $this->vatRepository->update($input, $id);

        return $this->sendResponse($vat->toArray(), trans('vat.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Vat $vat */
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            return $this->sendError(trans('vat.not_found'));
        }

        $vat->delete();

        return $this->sendResponse($id, trans('vat.message_deleted_successfully'));
    }
}
