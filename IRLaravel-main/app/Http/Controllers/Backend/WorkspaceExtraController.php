<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateWorkspaceExtraRequest;
use App\Http\Requests\UpdateWorkspaceExtraRequest;
use App\Repositories\WorkspaceExtraRepository;
use App\Repositories\SettingPreferenceRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WorkspaceExtraController extends BaseController
{
    /** @var  WorkspaceExtraRepository */
    private $workspaceExtraRepository;
    /** @var SettingPreferenceRepository */
    private $settingPreferenceRepository;

    public function __construct(WorkspaceExtraRepository $workspaceExtraRepo, SettingPreferenceRepository $settingPreferenceRepo)
    {
        parent::__construct();

        $this->workspaceExtraRepository = $workspaceExtraRepo;
        $this->settingPreferenceRepository = $settingPreferenceRepo;
    }

    /**
     * Display a listing of the WorkspaceExtra.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->workspaceExtraRepository->pushCriteria(new RequestCriteria($request));
        $workspaceExtras = $this->workspaceExtraRepository->all();

        return view('admin.workspace_extras.index')
            ->with('workspaceExtras', $workspaceExtras);
    }

    /**
     * Show the form for creating a new WorkspaceExtra.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.workspace_extras.create');
    }

    /**
     * Store a newly created WorkspaceExtra in storage.
     *
     * @param CreateWorkspaceExtraRequest $request
     *
     * @return Response
     */
    public function store(CreateWorkspaceExtraRequest $request)
    {
        $input = $request->all();

        $workspaceExtra = $this->workspaceExtraRepository->create($input);

        Flash::success(trans('workspace_extra.message_saved_successfully'));

        return redirect(route('admin.workspaceExtras.index'));
    }

    /**
     * Display the specified WorkspaceExtra.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            Flash::error(trans('workspace_extra.not_found'));

            return redirect(route('admin.workspaceExtras.index'));
        }

        return view('admin.workspace_extras.show')->with('workspaceExtra', $workspaceExtra);
    }

    /**
     * Show the form for editing the specified WorkspaceExtra.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            Flash::error(trans('workspace_extra.not_found'));

            return redirect(route('admin.workspaceExtras.index'));
        }

        return view('admin.workspace_extras.edit')->with('workspaceExtra', $workspaceExtra);
    }

    /**
     * Update the specified WorkspaceExtra in storage.
     *
     * @param  int              $id
     * @param UpdateWorkspaceExtraRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWorkspaceExtraRequest $request)
    {
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            Flash::error(trans('workspace_extra.not_found'));

            return redirect(route('admin.workspaceExtras.index'));
        }

        $workspaceExtra = $this->workspaceExtraRepository->update($request->all(), $id);

        Flash::success(trans('workspace_extra.message_updated_successfully'));

        return redirect(route('admin.workspaceExtras.index'));
    }

    /**
     * Remove the specified WorkspaceExtra from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            Flash::error(trans('workspace_extra.not_found'));

            return redirect(route('admin.workspaceExtras.index'));
        }

        $this->workspaceExtraRepository->delete($id);

        Flash::success(trans('workspace_extra.message_deleted_successfully'));

        return redirect(route('admin.workspaceExtras.index'));
    }

    /**
     * @param int $workspaceId
     * @param int $type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrCreate(int $workspaceId, int $type, Request $request)
    {
        $input = $request->all();
        $input['type'] = $type;
        $input['active'] = $request->status;

        $workspaceExtra = $this->workspaceExtraRepository->updateOrCreate(
            [
                'type' => $type,
                'workspace_id' => $workspaceId,
            ],
            $input
        );

        if($type === \App\Models\WorkspaceExtra::SERVICE_COST && $workspaceExtra->active === false) {
            $this->settingPreferenceRepository->updateAndWhere([
                'workspace_id' => $workspaceId
            ], [
                'service_cost_set' => false,
                'service_cost' => 0,
                'service_cost_amount' => 0,
                'service_cost_always_charge' => false
            ]);
        }

        $response = array(
            //return status result from db
            'status' => $workspaceExtra->active,
        );

        return response()->json($response);
    }
}
