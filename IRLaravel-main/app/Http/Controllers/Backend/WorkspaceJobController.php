<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateWorkspaceJobRequest;
use App\Http\Requests\UpdateWorkspaceJobRequest;
use App\Repositories\WorkspaceJobRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WorkspaceJobController extends BaseController
{
    /** @var  WorkspaceJobRepository */
    private $workspaceJobRepository;

    public function __construct(WorkspaceJobRepository $workspaceJobRepo)
    {
        parent::__construct();

        $this->workspaceJobRepository = $workspaceJobRepo;
    }

    /**
     * Display a listing of the WorkspaceJob.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->workspaceJobRepository->pushCriteria(new RequestCriteria($request));
        $workspaceJobs = $this->workspaceJobRepository->all();

        return view('admin.workspace_jobs.index')
            ->with('workspaceJobs', $workspaceJobs);
    }

    /**
     * Show the form for creating a new WorkspaceJob.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.workspace_jobs.create');
    }

    /**
     * Store a newly created WorkspaceJob in storage.
     *
     * @param CreateWorkspaceJobRequest $request
     *
     * @return Response
     */
    public function store(CreateWorkspaceJobRequest $request)
    {
        $input = $request->all();

        $workspaceJob = $this->workspaceJobRepository->create($input);

        Flash::success(trans('workspace_job.message_saved_successfully'));

        return redirect(route('admin.workspaceJobs.index'));
    }

    /**
     * Display the specified WorkspaceJob.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            Flash::error(trans('workspace_job.not_found'));

            return redirect(route('admin.workspaceJobs.index'));
        }

        return view('admin.workspace_jobs.show')->with('workspaceJob', $workspaceJob);
    }

    /**
     * Show the form for editing the specified WorkspaceJob.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            Flash::error(trans('workspace_job.not_found'));

            return redirect(route('admin.workspaceJobs.index'));
        }

        return view('admin.workspace_jobs.edit')->with('workspaceJob', $workspaceJob);
    }

    /**
     * Update the specified WorkspaceJob in storage.
     *
     * @param  int              $id
     * @param UpdateWorkspaceJobRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWorkspaceJobRequest $request)
    {
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            Flash::error(trans('workspace_job.not_found'));

            return redirect(route('admin.workspaceJobs.index'));
        }

        $workspaceJob = $this->workspaceJobRepository->update($request->all(), $id);

        Flash::success(trans('workspace_job.message_updated_successfully'));

        return redirect(route('admin.workspaceJobs.index'));
    }

    /**
     * Remove the specified WorkspaceJob from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            Flash::error(trans('workspace_job.not_found'));

            return redirect(route('admin.workspaceJobs.index'));
        }

        $this->workspaceJobRepository->delete($id);

        Flash::success(trans('workspace_job.message_deleted_successfully'));

        return redirect(route('admin.workspaceJobs.index'));
    }
}
