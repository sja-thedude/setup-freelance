<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateWorkspaceCategoryRequest;
use App\Http\Requests\UpdateWorkspaceCategoryRequest;
use App\Repositories\WorkspaceCategoryRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WorkspaceCategoryController extends BaseController
{
    /** @var  WorkspaceCategoryRepository */
    private $workspaceCategoryRepository;

    public function __construct(WorkspaceCategoryRepository $workspaceCategoryRepo)
    {
        parent::__construct();

        $this->workspaceCategoryRepository = $workspaceCategoryRepo;
    }

    /**
     * Display a listing of the WorkspaceCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->workspaceCategoryRepository->pushCriteria(new RequestCriteria($request));
        $workspaceCategories = $this->workspaceCategoryRepository->all();

        return view('admin.workspace_categories.index')
            ->with('workspaceCategories', $workspaceCategories);
    }

    /**
     * Show the form for creating a new WorkspaceCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.workspace_categories.create');
    }

    /**
     * Store a newly created WorkspaceCategory in storage.
     *
     * @param CreateWorkspaceCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateWorkspaceCategoryRequest $request)
    {
        $input = $request->all();

        $workspaceCategory = $this->workspaceCategoryRepository->create($input);

        Flash::success(trans('workspace_category.message_saved_successfully'));

        return redirect(route('admin.workspaceCategories.index'));
    }

    /**
     * Display the specified WorkspaceCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            Flash::error(trans('workspace_category.not_found'));

            return redirect(route('admin.workspaceCategories.index'));
        }

        return view('admin.workspace_categories.show')->with('workspaceCategory', $workspaceCategory);
    }

    /**
     * Show the form for editing the specified WorkspaceCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            Flash::error(trans('workspace_category.not_found'));

            return redirect(route('admin.workspaceCategories.index'));
        }

        return view('admin.workspace_categories.edit')->with('workspaceCategory', $workspaceCategory);
    }

    /**
     * Update the specified WorkspaceCategory in storage.
     *
     * @param  int              $id
     * @param UpdateWorkspaceCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWorkspaceCategoryRequest $request)
    {
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            Flash::error(trans('workspace_category.not_found'));

            return redirect(route('admin.workspaceCategories.index'));
        }

        $workspaceCategory = $this->workspaceCategoryRepository->update($request->all(), $id);

        Flash::success(trans('workspace_category.message_updated_successfully'));

        return redirect(route('admin.workspaceCategories.index'));
    }

    /**
     * Remove the specified WorkspaceCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            Flash::error(trans('workspace_category.not_found'));

            return redirect(route('admin.workspaceCategories.index'));
        }

        $this->workspaceCategoryRepository->delete($id);

        Flash::success(trans('workspace_category.message_deleted_successfully'));

        return redirect(route('admin.workspaceCategories.index'));
    }
}
