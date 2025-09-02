<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceExtraAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceExtraAPIRequest;
use App\Models\WorkspaceExtra;
use App\Repositories\WorkspaceExtraRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WorkspaceExtraController
 * @package App\Http\Controllers\API
 */
class WorkspaceExtraAPIController extends AppBaseController
{
    /** @var  WorkspaceExtraRepository */
    private $workspaceExtraRepository;

    public function __construct(WorkspaceExtraRepository $workspaceExtraRepo)
    {
        parent::__construct();

        $this->workspaceExtraRepository = $workspaceExtraRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->workspaceExtraRepository->pushCriteria(new RequestCriteria($request));
            $this->workspaceExtraRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $workspaceExtras = $this->workspaceExtraRepository->paginate($limit);

        return $this->sendResponse($workspaceExtras->toArray(), trans('workspace_extra.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateWorkspaceExtraAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceExtraAPIRequest $request)
    {
        $input = $request->all();

        $workspaceExtra = $this->workspaceExtraRepository->create($input);

        return $this->sendResponse($workspaceExtra->toArray(), trans('workspace_extra.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var WorkspaceExtra $workspaceExtra */
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            return $this->sendError(trans('workspace_extra.not_found'));
        }

        return $this->sendResponse($workspaceExtra->toArray(), trans('workspace_extra.message_retrieved_successfully'));
    }

    /**
     * @param UpdateWorkspaceExtraAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceExtraAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var WorkspaceExtra $workspaceExtra */
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            return $this->sendError(trans('workspace_extra.not_found'));
        }

        $workspaceExtra = $this->workspaceExtraRepository->update($input, $id);

        return $this->sendResponse($workspaceExtra->toArray(), trans('workspace_extra.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WorkspaceExtra $workspaceExtra */
        $workspaceExtra = $this->workspaceExtraRepository->findWithoutFail($id);

        if (empty($workspaceExtra)) {
            return $this->sendError(trans('workspace_extra.not_found'));
        }

        $workspaceExtra->delete();

        return $this->sendResponse($id, trans('workspace_extra.message_deleted_successfully'));
    }
}
