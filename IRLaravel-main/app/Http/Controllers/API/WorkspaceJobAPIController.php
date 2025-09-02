<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceJobAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceJobAPIRequest;
use App\Models\WorkspaceJob;
use App\Repositories\WorkspaceJobRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WorkspaceJobController
 * @package App\Http\Controllers\API
 */
class WorkspaceJobAPIController extends AppBaseController
{
    /** @var  WorkspaceJobRepository */
    private $workspaceJobRepository;

    public function __construct(WorkspaceJobRepository $workspaceJobRepo)
    {
        parent::__construct();

        $this->workspaceJobRepository = $workspaceJobRepo;
    }

    /**
     * @api {get} /workspace_jobs List
     * @apiGroup WorkspaceJob
     * @apiName List
     * @apiDescription Display a listing of the WorkspaceJob.
     *
     * @apiPermission api
     * @apiPermission jwt.auth
     *
     * @apiHeader {String} Authorization Authorization Header
     *
     * @apiParam {Request} request
     *
     * @apiSuccess {Number} id Id of WorkspaceJob
     * @apiSuccess {String} created_at Created at of WorkspaceJob
     * @apiSuccess {String} updated_at Updated at of WorkspaceJob
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "meta": {
     *             "success": true,
     *             "message": "success",
     *             "code": 200
     *         },
     *         "response": {
     *             "total": 19,
     *             "per_page": 5,
     *             "current_page": 1,
     *             "last_page": 4,
     *             "next_page_url": null,
     *             "prev_page_url": null,
     *             "from": 1,
     *             "to": 5,
     *             "data": [
     *                 {
     *                     "id": 23,
     *                     "created_at": "2018-06-01 05:41:11",
     *                     "updated_at": "2018-06-01 05:41:11"
     *                 }
     *             ]
     *         }
     *     }
     *
     * @apiError Error Get list failure
     * @apiErrorExample {json} Error-Response 500:
     *     HTTP/1.1 500 GetErrorFailure
     *     {
     *        "meta": {
     *            "success": false,
     *            "message": "failure",
     *            "code": 500,
     *            "errors": null
     *        },
     *        "response": null
     *     }
     *
     */
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->workspaceJobRepository->pushCriteria(new RequestCriteria($request));
            $this->workspaceJobRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $workspaceJobs = $this->workspaceJobRepository->paginate($limit);

        return $this->sendResponse($workspaceJobs->toArray(), 'Workspace Jobs are retrieved successfully');
    }

    /**
      * @api {post} /workspace_jobs Create
      * @apiGroup WorkspaceJob
      * @apiName StoreWorkspaceJob
      * @apiDescription Store a newly created WorkspaceJob in storage.
      *
      * @apiPermission api
      * @apiPermission jwt.auth
      *
      * @apiHeader {String} Authorization Authorization Header
      *
      * @apiParam {CreateWorkspaceJobAPIRequest} request
      *
      * @apiSuccess {Number} id Id of WorkspaceJob
      * @apiSuccess {String} created_at Created at of WorkspaceJob
      * @apiSuccess {String} updated_at Updated at of WorkspaceJob
      *
      * @apiSuccessExample {json} Success-Response:
      *     HTTP/1.1 200 OK
      *     {
      *          "meta": {
      *             "success": true,
      *             "message": "success",
      *             "code": 200
      *          },
      *          "response": {
      *              "id": 1,
      *              "created_at": "2018-05-16 14:32:15",
      *              "updated_at": "2018-05-16 14:32:15"
      *          }
      *     }
      *
      * @apiError Error Get list failure
      * @apiErrorExample {json} Error-Response 500:
      *     HTTP/1.1 500 GetErrorFailure
      *     {
      *        "meta": {
      *            "success": false,
      *            "message": "failure",
      *            "code": 500,
      *            "errors": null
      *        },
      *        "response": null
      *     }
      *
      */
    /**
     * @param CreateWorkspaceJobAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceJobAPIRequest $request)
    {
        $input = $request->all();

        $workspaceJob = $this->workspaceJobRepository->create($input);

        return $this->sendResponse($workspaceJob->toArray(), 'Workspace Job is saved successfully');
    }

    /**
      * @api {get} /workspace_jobs/:id Detail
      * @apiGroup WorkspaceJob
      * @apiName ShowWorkspaceJob
      * @apiDescription Display the specified WorkspaceJob.
      *
      * @apiPermission api
      * @apiPermission jwt.auth
      *
      * @apiHeader {String} Authorization Authorization Header
      *
      * @apiParam {Number} id Id of WorkspaceJob
      *
      * @apiSuccess {Number} id Id of WorkspaceJob
      * @apiSuccess {String} created_at Created at of WorkspaceJob
      * @apiSuccess {String} updated_at Updated at of WorkspaceJob
      *
      * @apiSuccessExample {json} Success-Response:
      *     HTTP/1.1 200 OK
      *     {
      *          "meta": {
      *             "success": true,
      *             "message": "success",
      *             "code": 200
      *          },
      *          "response": {
      *              "id": 1,
      *              "created_at": "2018-05-16 14:32:15",
      *              "updated_at": "2018-05-16 14:32:15"
      *          }
      *     }
      *
      * @apiError Error Get list failure
      * @apiErrorExample {json} Error-Response 500:
      *     HTTP/1.1 500 GetErrorFailure
      *     {
      *        "meta": {
      *            "success": false,
      *            "message": "failure",
      *            "code": 500,
      *            "errors": null
      *        },
      *        "response": null
      *     }
      *
      */
    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var WorkspaceJob $workspaceJob */
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            return $this->sendError('Workspace Job not found');
        }

        return $this->sendResponse($workspaceJob->toArray(), 'Workspace Job is retrieved successfully');
    }

    /**
     * @api {put} /workspace_jobs/:id Update
     * @apiGroup WorkspaceJob
     * @apiName UpdateWorkspaceJob
     * @apiDescription Update the specified WorkspaceJob in storage.
     *
     * @apiPermission api
     * @apiPermission jwt.auth
     *
     * @apiHeader {String} Authorization Authorization Header
     *
     * @apiParam {UpdateWorkspaceJobAPIRequest} request
     *
     * @apiSuccess {Number} id Id of WorkspaceJob
     * @apiSuccess {String} created_at Created at of WorkspaceJob
     * @apiSuccess {String} updated_at Updated at of WorkspaceJob
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "meta": {
     *             "success": true,
     *             "message": "success",
     *             "code": 200
     *          },
     *          "response": {
     *              "id": 1,
     *              "created_at": "2018-05-16 14:32:15",
     *              "updated_at": "2018-05-16 14:32:15"
     *          }
     *     }
     *
     * @apiError Error Get list failure
     * @apiErrorExample {json} Error-Response 500:
     *     HTTP/1.1 500 GetErrorFailure
     *     {
     *        "meta": {
     *            "success": false,
     *            "message": "failure",
     *            "code": 500,
     *            "errors": null
     *        },
     *        "response": null
     *     }
     *
     */
    /**
     * @param UpdateWorkspaceJobAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceJobAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var WorkspaceJob $workspaceJob */
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            return $this->sendError('Workspace Job not found');
        }

        $workspaceJob = $this->workspaceJobRepository->update($input, $id);

        return $this->sendResponse($workspaceJob->toArray(), 'WorkspaceJob is updated successfully');
    }

    /**
     * @api {delete} /workspace_jobs/:id Destroy
     * @apiGroup WorkspaceJob
     * @apiName DestroyWorkspaceJob
     * @apiDescription Remove the specified WorkspaceJob from storage.
     *
     * @apiPermission api
     * @apiPermission jwt.auth
     *
     * @apiHeader {String} Authorization Authorization Header
     *
     * @apiParam {Number} id Id of WorkspaceJob
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "meta": {
     *             "success": true,
     *             "message": "success",
     *             "code": 200
     *          },
     *          "response": null
     *     }
     *
     * @apiError Error Get list failure
     * @apiErrorExample {json} Error-Response 500:
     *     HTTP/1.1 500 GetErrorFailure
     *     {
     *        "meta": {
     *            "success": false,
     *            "message": "failure",
     *            "code": 500,
     *            "errors": null
     *        },
     *        "response": null
     *     }
     *
     */
    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WorkspaceJob $workspaceJob */
        $workspaceJob = $this->workspaceJobRepository->findWithoutFail($id);

        if (empty($workspaceJob)) {
            return $this->sendError('Workspace Job not found');
        }

        $workspaceJob->delete();

        return $this->sendResponse($id, 'Workspace Job is deleted successfully');
    }
}
