<?php

namespace App\Http\Controllers\API\Workspace;

use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\CreateWorkspaceJobAPIRequest;
use App\Repositories\WorkspaceJobRepository;

/**
 * Class JobAPIController
 * @package App\Http\Controllers\API\Workspace
 */
class JobAPIController extends AppBaseController
{
    /**
     * @var WorkspaceJobRepository $workspaceJobRepository
     */
    protected $workspaceJobRepository;

    /**
     * JobAPIController constructor.
     * @param WorkspaceJobRepository $workspaceJobRepo
     */
    public function __construct(WorkspaceJobRepository $workspaceJobRepo)
    {
        parent::__construct();

        $this->workspaceJobRepository = $workspaceJobRepo;
    }

    /**
     * @param CreateWorkspaceJobAPIRequest $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateWorkspaceJobAPIRequest $request, int $workspaceId)
    {
        $request->merge([
            'workspace_id' => $workspaceId
        ]);

        // Get timezone from request header
        if (!$request->has('timezone') && $request->hasHeader('Timezone')) {
            $timezone = $request->header('Timezone', config('app.timezone'));
            $request->merge([
                'timezone' => $timezone,
            ]);
        }

        $input = $request->all();

        $workspaceJob = $this->workspaceJobRepository->create($input);

        $result = $workspaceJob->getFullInfo();

        return $this->sendResponse($result, trans('workspace_job.message_sent_successfully'));
    }

}
