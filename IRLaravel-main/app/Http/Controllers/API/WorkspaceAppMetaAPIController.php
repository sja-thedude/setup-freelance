<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceAppMetaAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceAppMetaAPIRequest;
use App\Models\WorkspaceAppMeta;
use App\Repositories\WorkspaceAppMetaRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WorkspaceAppMetaController
 * @package App\Http\Controllers\API
 */
class WorkspaceAppMetaAPIController extends AppBaseController
{
    /** @var  WorkspaceAppMetaRepository */
    private $workspaceAppMetaRepository;

    public function __construct(WorkspaceAppMetaRepository $workspaceAppMetaRepo)
    {
        parent::__construct();

        $this->workspaceAppMetaRepository = $workspaceAppMetaRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->workspaceAppMetaRepository->pushCriteria(new RequestCriteria($request));
            $this->workspaceAppMetaRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $workspaceAppMetas = $this->workspaceAppMetaRepository->paginate($limit);

        return $this->sendResponse($workspaceAppMetas->toArray(), trans('workspace_app_meta.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateWorkspaceAppMetaAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceAppMetaAPIRequest $request)
    {
        $input = $request->all();

        $workspaceAppMeta = $this->workspaceAppMetaRepository->create($input);

        return $this->sendResponse($workspaceAppMeta->toArray(), trans('workspace_app_meta.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var WorkspaceAppMeta $workspaceAppMeta */
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            return $this->sendError(trans('workspace_app_meta.not_found'));
        }

        return $this->sendResponse($workspaceAppMeta->toArray(), trans('workspace_app_meta.message_retrieved_successfully'));
    }

    /**
     * @param UpdateWorkspaceAppMetaAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceAppMetaAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var WorkspaceAppMeta $workspaceAppMeta */
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            return $this->sendError(trans('workspace_app_meta.not_found'));
        }

        $workspaceAppMeta = $this->workspaceAppMetaRepository->update($input, $id);

        return $this->sendResponse($workspaceAppMeta->toArray(), trans('workspace_app_meta.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WorkspaceAppMeta $workspaceAppMeta */
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            return $this->sendError(trans('workspace_app_meta.not_found'));
        }

        $workspaceAppMeta->delete();

        return $this->sendResponse($id, trans('workspace_app_meta.message_deleted_successfully'));
    }
}
