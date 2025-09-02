<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateWorkspaceAppMetaRequest;
use App\Http\Requests\UpdateWorkspaceAppMetaRequest;
use App\Repositories\WorkspaceAppMetaRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WorkspaceAppMetaController extends BaseController
{
    /** @var  WorkspaceAppMetaRepository */
    private $workspaceAppMetaRepository;

    public function __construct(WorkspaceAppMetaRepository $workspaceAppMetaRepo)
    {
        parent::__construct();

        $this->workspaceAppMetaRepository = $workspaceAppMetaRepo;
    }

    /**
     * Display a listing of the WorkspaceAppMeta.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->workspaceAppMetaRepository->pushCriteria(new RequestCriteria($request));
        $workspaceAppMetas = $this->workspaceAppMetaRepository->all();

        return view('admin.workspace_app_metas.index')
            ->with('workspaceAppMetas', $workspaceAppMetas);
    }

    /**
     * Show the form for creating a new WorkspaceAppMeta.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.workspace_app_metas.create');
    }

    /**
     * Store a newly created WorkspaceAppMeta in storage.
     *
     * @param CreateWorkspaceAppMetaRequest $request
     *
     * @return Response
     */
    public function store(CreateWorkspaceAppMetaRequest $request)
    {
        $input = $request->all();

        $workspaceAppMeta = $this->workspaceAppMetaRepository->create($input);

        Flash::success(trans('workspace_app_meta.message_saved_successfully'));

        return redirect(route('admin.workspaceAppMetas.index'));
    }

    /**
     * Display the specified WorkspaceAppMeta.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app_meta.not_found'));

            return redirect(route('admin.workspaceAppMetas.index'));
        }

        return view('admin.workspace_app_metas.show')->with('workspaceAppMeta', $workspaceAppMeta);
    }

    /**
     * Show the form for editing the specified WorkspaceAppMeta.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app_meta.not_found'));

            return redirect(route('admin.workspaceAppMetas.index'));
        }

        return view('admin.workspace_app_metas.edit')->with('workspaceAppMeta', $workspaceAppMeta);
    }

    /**
     * Update the specified WorkspaceAppMeta in storage.
     *
     * @param  int              $id
     * @param UpdateWorkspaceAppMetaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWorkspaceAppMetaRequest $request)
    {
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app_meta.not_found'));

            return redirect(route('admin.workspaceAppMetas.index'));
        }

        $workspaceAppMeta = $this->workspaceAppMetaRepository->update($request->all(), $id);

        Flash::success(trans('workspace_app_meta.message_updated_successfully'));

        return redirect(route('admin.workspaceAppMetas.index'));
    }

    /**
     * Remove the specified WorkspaceAppMeta from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app_meta.not_found'));

            return redirect(route('admin.workspaceAppMetas.index'));
        }

        $this->workspaceAppMetaRepository->delete($id);

        Flash::success(trans('workspace_app_meta.message_deleted_successfully'));

        return redirect(route('admin.workspaceAppMetas.index'));
    }
}
