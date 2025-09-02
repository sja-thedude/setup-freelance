<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePrinterGroupAPIRequest;
use App\Http\Requests\API\UpdatePrinterGroupAPIRequest;
use App\Models\PrinterGroup;
use App\Repositories\PrinterGroupRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PrinterGroupController
 * @package App\Http\Controllers\API
 */
class PrinterGroupAPIController extends AppBaseController
{
    /** @var  PrinterGroupRepository */
    private $printerGroupRepository;

    public function __construct(PrinterGroupRepository $printerGroupRepo)
    {
        parent::__construct();

        $this->printerGroupRepository = $printerGroupRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->printerGroupRepository->pushCriteria(new RequestCriteria($request));
            $this->printerGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $printerGroups = $this->printerGroupRepository->paginate($limit);

        return $this->sendResponse($printerGroups->toArray(), trans('printer_group.message_retrieved_list_successfully'));
    }

    /**
     * @param CreatePrinterGroupAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePrinterGroupAPIRequest $request)
    {
        $input = $request->all();

        $printerGroup = $this->printerGroupRepository->create($input);

        return $this->sendResponse($printerGroup->toArray(), trans('printer_group.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var PrinterGroup $printerGroup */
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            return $this->sendError('Printer Group not found');
        }

        return $this->sendResponse($printerGroup->toArray(), trans('printer_group.message_retried_successfully'));
    }

    /**
     * @param UpdatePrinterGroupAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePrinterGroupAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var PrinterGroup $printerGroup */
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            return $this->sendError('Printer Group not found');
        }

        $printerGroup = $this->printerGroupRepository->update($input, $id);

        return $this->sendResponse($printerGroup->toArray(), trans('printer_group.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var PrinterGroup $printerGroup */
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            return $this->sendError('Printer Group not found');
        }

        $printerGroup->delete();

        return $this->sendResponse($id, trans('printer_group.message_deleted_successfully'));
    }
}
