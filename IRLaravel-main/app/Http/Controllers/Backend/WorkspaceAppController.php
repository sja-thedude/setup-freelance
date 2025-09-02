<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateWorkspaceAppRequest;
use App\Http\Requests\UpdateWorkspaceAppRequest;
use App\Repositories\WorkspaceAppRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WorkspaceAppController extends BaseController
{
    /** @var  WorkspaceAppRepository */
    private $workspaceAppRepository;

    public function __construct(WorkspaceAppRepository $workspaceAppRepo)
    {
        parent::__construct();

        $this->workspaceAppRepository = $workspaceAppRepo;
    }

    /**
     * Display a listing of the WorkspaceApp.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->workspaceAppRepository->pushCriteria(new RequestCriteria($request));
        $workspaceApps = $this->workspaceAppRepository->all();

        return view('admin.workspace_apps.index')
            ->with('workspaceApps', $workspaceApps);
    }

    /**
     * Show the form for creating a new WorkspaceApp.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.workspace_apps.create');
    }

    /**
     * Store a newly created WorkspaceApp in storage.
     *
     * @param CreateWorkspaceAppRequest $request
     *
     * @return Response
     */
    public function store(CreateWorkspaceAppRequest $request)
    {
        $input = $request->all();

        $workspaceApp = $this->workspaceAppRepository->create($input);

        Flash::success(trans('workspace_app.message_saved_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Display the specified WorkspaceApp.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        return view('admin.workspace_apps.show')->with('workspaceApp', $workspaceApp);
    }

    /**
     * Show the form for editing the specified WorkspaceApp.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        return view('admin.workspace_apps.edit')->with('workspaceApp', $workspaceApp);
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param  int              $id
     * @param UpdateWorkspaceAppRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWorkspaceAppRequest $request)
    {
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        $workspaceApp = $this->workspaceAppRepository->update($request->all(), $id);

        Flash::success(trans('workspace_app.message_updated_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Remove the specified WorkspaceApp from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        $this->workspaceAppRepository->delete($id);

        Flash::success(trans('workspace_app.message_deleted_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }
}
