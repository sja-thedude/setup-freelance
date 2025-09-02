<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceCategoryAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceCategoryAPIRequest;
use App\Models\WorkspaceCategory;
use App\Repositories\WorkspaceCategoryRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WorkspaceCategoryController
 * @package App\Http\Controllers\API
 */
class WorkspaceCategoryAPIController extends AppBaseController
{
    /** @var  WorkspaceCategoryRepository */
    private $workspaceCategoryRepository;

    public function __construct(WorkspaceCategoryRepository $workspaceCategoryRepo)
    {
        parent::__construct();

        $this->workspaceCategoryRepository = $workspaceCategoryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->workspaceCategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->workspaceCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $workspaceCategories = $this->workspaceCategoryRepository->paginate($limit);

        return $this->sendResponse($workspaceCategories->toArray(), trans('workspace_category.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateWorkspaceCategoryAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceCategoryAPIRequest $request)
    {
        $input = $request->all();

        $workspaceCategory = $this->workspaceCategoryRepository->create($input);

        return $this->sendResponse($workspaceCategory->toArray(), trans('workspace_category.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var WorkspaceCategory $workspaceCategory */
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            return $this->sendError(trans('work_space_category.not_found'));
        }

        return $this->sendResponse($workspaceCategory->toArray(), trans('workspace_category.message_retrieved_successfully'));
    }

    /**
     * @param UpdateWorkspaceCategoryAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceCategoryAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var WorkspaceCategory $workspaceCategory */
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            return $this->sendError(trans('work_space_category.not_found'));
        }

        $workspaceCategory = $this->workspaceCategoryRepository->update($input, $id);

        return $this->sendResponse($workspaceCategory->toArray(), trans('workspace_category.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WorkspaceCategory $workspaceCategory */
        $workspaceCategory = $this->workspaceCategoryRepository->findWithoutFail($id);

        if (empty($workspaceCategory)) {
            return $this->sendError(trans('work_space_category.not_found'));
        }

        $workspaceCategory->delete();

        return $this->sendResponse($id, trans('work_space_category.message_deleted_successfully'));
    }
}
