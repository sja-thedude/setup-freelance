<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceAppAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceAppAPIRequest;
use App\Models\WorkspaceApp;
use App\Repositories\WorkspaceAppRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WorkspaceAppController
 * @package App\Http\Controllers\API
 */
class WorkspaceAppAPIController extends AppBaseController
{
    /** @var  WorkspaceAppRepository */
    private $workspaceAppRepository;

    public function __construct(WorkspaceAppRepository $workspaceAppRepo)
    {
        parent::__construct();

        $this->workspaceAppRepository = $workspaceAppRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->workspaceAppRepository->pushCriteria(new RequestCriteria($request));
            $this->workspaceAppRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $workspaceApps = $this->workspaceAppRepository->paginate($limit);

        return $this->sendResponse($workspaceApps->toArray(), trans('workspace_app.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateWorkspaceAppAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceAppAPIRequest $request)
    {
        $input = $request->all();

        $workspaceApp = $this->workspaceAppRepository->create($input);

        return $this->sendResponse($workspaceApp->toArray(), trans('workspace_app.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            return $this->sendError(trans('workspace_app.not_found'));
        }

        return $this->sendResponse($workspaceApp->toArray(), trans('workspace_app.message_retrieved_successfully'));
    }

    /**
     * @param UpdateWorkspaceAppAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceAppAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            return $this->sendError(trans('workspace_app.not_found'));
        }

        $workspaceApp = $this->workspaceAppRepository->update($input, $id);

        return $this->sendResponse($workspaceApp->toArray(), trans('workspace_app.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->findWithoutFail($id);

        if (empty($workspaceApp)) {
            return $this->sendError(trans('workspace_app.not_found'));
        }

        $workspaceApp->delete();

        return $this->sendResponse($id, trans('workspace_app.message_deleted_successfully'));
    }
}
