<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreatePrinterGroupRequest;
use App\Http\Requests\UpdatePrinterGroupRequest;
use App\Repositories\PrinterGroupRepository;
use App\Repositories\PrinterGroupWorkspaceRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PrinterGroupController extends BaseController
{
    /** @var  PrinterGroupRepository */
    private $printerGroupRepository;
    private $printerGroupWorkspaceRepository;
    protected $workspaceRepository;

    public function __construct(
        PrinterGroupRepository $printerGroupRepo,
        PrinterGroupWorkspaceRepository $printerGroupWorkspaceRepo,
        WorkspaceRepository $workspaceRepo
    ) {
        parent::__construct();

        $this->printerGroupRepository = $printerGroupRepo;
        $this->printerGroupWorkspaceRepository = $printerGroupWorkspaceRepo;
        $this->workspaceRepository = $workspaceRepo;
    }

    /**
     * Display a listing of the PrinterGroup.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $exceptRestaurantIds = $this->printerGroupWorkspaceRepository->makeModel()->groupBy('workspace_id')->pluck('workspace_id')->all();
        $restaurants = $this->workspaceRepository->makeModel()->whereNotIn('id', $exceptRestaurantIds)->pluck('name', 'id')->all();
        $this->printerGroupRepository->pushCriteria(new RequestCriteria($request));
        $model = $this->printerGroupRepository->getList($request, $this->perPage);

        return view('admin.printer_groups.index')
            ->with(compact('model', 'restaurants'));
    }

    /**
     * Show the form for creating a new PrinterGroup.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.printer_groups.create');
    }

    /**
     * Store a newly created PrinterGroup in storage.
     *
     * @param CreatePrinterGroupRequest $request
     *
     * @return Response
     */
    public function store(CreatePrinterGroupRequest $request)
    {
        $input = $request->all();
        $workspaceIds = !empty($input['restaurants']) ? $input['restaurants'] : [];

        if(!empty($workspaceIds)) {
            $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->makeModel()
                ->whereIn('workspace_id', $workspaceIds)
                ->get();

            if (!$printerGroupWorkspace->isEmpty()) {
                Flash::error(trans('printer_group.one_restaurant_join_one_group'));
                return redirect(route('admin.printergroup.index'));
            }
        }

        try {
            \DB::beginTransaction();

            $printerGroup = $this->printerGroupRepository->create($input);
            $printerGroup->printerGroupWorkspaces()->sync($workspaceIds);

            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($printerGroup, trans('workspace.created_confirm'));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->sendError($e->getMessage(), 400);
        }

        return redirect(route('admin.printergroup.index'));
    }

    /**
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateStatus(int $id, Request $request)
    {
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            Flash::error(trans('printer_group.not_found'));
            return redirect(route($this->guard . '.printergroup.index'));
        }

        //Active - inactive status
        $input['active'] = (int)$request->status;
        $data = $this->printerGroupRepository->update($input, $id);
        $response = array(
            'data' => $data,
            'status' => $data->active,
        );

        return response()->json($response);
    }

    /**
     * Display the specified PrinterGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            Flash::error(trans('printer_group.not_found'));
            return redirect(route('admin.printerGroups.index'));
        }

        return view('admin.printer_groups.show')->with('printerGroup', $printerGroup);
    }

    /**
     * Show the form for editing the specified PrinterGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            Flash::error(trans('printer_group.not_found'));
            return redirect(route('admin.printerGroups.index'));
        }

        return view('admin.printer_groups.edit')->with('printerGroup', $printerGroup);
    }

    /**
     * Update the specified PrinterGroup in storage.
     *
     * @param  int              $id
     * @param UpdatePrinterGroupRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePrinterGroupRequest $request)
    {
        $printerGroup = $this->printerGroupRepository->findWithoutFail($id);

        if (empty($printerGroup)) {
            Flash::error(trans('printer_group.not_found'));
            return redirect(route('admin.printergroup.index'));
        }

        $input = $request->all();
        $workspaceIds = !empty($input['restaurants']) ? $input['restaurants'] : [];

        if(!empty($workspaceIds)) {
            $printerGroupWorkspace = $this->printerGroupWorkspaceRepository->makeModel()
                ->whereIn('workspace_id', $workspaceIds)
                ->where('printer_group_id', '!=', $printerGroup->id)
                ->get();

            if (!$printerGroupWorkspace->isEmpty()) {
                Flash::error(trans('printer_group.one_restaurant_join_one_group'));
                return redirect(route('admin.printergroup.index'));
            }
        }

        $input['active'] = 1;

        try {
            \DB::beginTransaction();

            $printerGroup = $this->printerGroupRepository->update($input, $id);
            $printerGroup->printerGroupWorkspaces()->sync($workspaceIds);

            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($printerGroup, trans('workspace.updated_confirm'));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->sendError($e->getMessage(), 400);
        }

        return redirect(route('admin.printergroup.index'));
    }

    /**
     * Remove the specified PrinterGroup from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $delete = $this->printerGroupRepository->delete($id);

        return $this->sendResponse($delete, trans('printer_group.deleted_confirm'));
    }
}
