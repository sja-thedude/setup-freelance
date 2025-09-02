<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePrinterGroupWorkspaceAPIRequest;
use App\Http\Requests\API\UpdatePrinterGroupWorkspaceAPIRequest;
use App\Models\PrinterGroupWorkspace;
use App\Repositories\PrinterGroupWorkspaceRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PrinterGroupWorkspaceController
 * @package App\Http\Controllers\API
 */
class PrinterGroupWorkspaceAPIController extends AppBaseController
{
    /** @var  PrinterGroupWorkspaceRepository */
    private $printerGroupWorkspaceRepository;

    public function __construct(PrinterGroupWorkspaceRepository $printerGroupWorkspaceRepo)
    {
        parent::__construct();

        $this->printerGroupWorkspaceRepository = $printerGroupWorkspaceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->printerGroupWorkspaceRepository->pushCriteria(new RequestCriteria($request));
            $this->printerGroupWorkspaceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $printerGroupWorkspaces = $this->printerGroupWorkspaceRepository->paginate($limit);

        return $this->sendResponse($printerGroupWorkspaces->toArray(), 'Printer Group Workspaces are retrieved successfully');
    }

    /**
     * @param CreatePrinterGroupWorkspaceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePrinterGroupWorkspaceAPIRequest $request)
    {
        $input = $request->all();

        $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->create($input);

        return $this->sendResponse($printerGroupWorkspace->toArray(), 'Printer Group Workspace is saved successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var PrinterGroupWorkspace $printerGroupWorkspace */
        $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->findWithoutFail($id);

        if (empty($printerGroupWorkspace)) {
            return $this->sendError('Printer Group Workspace not found');
        }

        return $this->sendResponse($printerGroupWorkspace->toArray(), 'Printer Group Workspace is retrieved successfully');
    }

    /**
     * @param UpdatePrinterGroupWorkspaceAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePrinterGroupWorkspaceAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var PrinterGroupWorkspace $printerGroupWorkspace */
        $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->findWithoutFail($id);

        if (empty($printerGroupWorkspace)) {
            return $this->sendError('Printer Group Workspace not found');
        }

        $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->update($input, $id);

        return $this->sendResponse($printerGroupWorkspace->toArray(), 'PrinterGroupWorkspace is updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var PrinterGroupWorkspace $printerGroupWorkspace */
        $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->findWithoutFail($id);

        if (empty($printerGroupWorkspace)) {
            return $this->sendError('Printer Group Workspace not found');
        }

        $printerGroupWorkspace->delete();

        return $this->sendResponse($id, 'Printer Group Workspace is deleted successfully');
    }
}
