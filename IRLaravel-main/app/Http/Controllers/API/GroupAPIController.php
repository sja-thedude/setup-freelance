<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGroupAPIRequest;
use App\Http\Requests\API\UpdateGroupAPIRequest;
use App\Models\Group;
use App\Repositories\GroupRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class GroupController
 * @package App\Http\Controllers\API
 */
class GroupAPIController extends AppBaseController
{
    /** @var GroupRepository $groupRepository */
    protected $groupRepository;

    /**
     * GroupAPIController constructor.
     * @param GroupRepository $groupRepo
     */
    public function __construct(GroupRepository $groupRepo)
    {
        parent::__construct();

        $this->groupRepository = $groupRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->groupRepository->pushCriteria(new RequestCriteria($request));
        $this->groupRepository->pushCriteria(new LimitOffsetCriteria($request));

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $groups = $this->groupRepository->paginate($limit, ['*'], 'paginate', ['workspace']);

        // Filter field by request type
        // Type: list
        $requestType = $request->get('requset_type');

        $groups->transform(function ($item) use ($requestType) {
            /** @var \App\Models\Group $item */

            if ($requestType == 'list') {
                // Key - value
                $data = $item->getListInfo();
            } else {
                // Get full information
                $data = $item->getFullInfo();
            }

            return $data;
        });
        $result = $groups->toArray();

        return $this->sendResponse($result, trans('group.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateGroupAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateGroupAPIRequest $request)
    {
        $input = $request->all();

        $group = $this->groupRepository->create($input);

        return $this->sendResponse($group->toArray(), trans('group.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Group $group */
        $group = $this->groupRepository
            ->with(['workspace', 'openTimeSlots'])
            ->findWithoutFail($id);

        if (empty($group)) {
            return $this->sendError(trans('group.not_found'));
        }

        $result = array_merge($group->getFullInfo(), [
            // Timeslot of group
            'timeslots' => $group->openTimeSlots->transform(function ($timeslot) {
                /** @var \App\Models\OpenTimeslot $timeslot */
                return $timeslot->getFullInfo();
            }),
            'products' => $group->getProductsBasedOnToggle()
        ]);

        return $this->sendResponse($result, trans('group.message_retrieved_successfully'));
    }

    /**
     * @param UpdateGroupAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGroupAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Group $group */
        $group = $this->groupRepository->findWithoutFail($id);

        if (empty($group)) {
            return $this->sendError('Group not found');
        }

        $group = $this->groupRepository->update($input, $id);

        return $this->sendResponse($group->toArray(), trans('group.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Group $group */
        $group = $this->groupRepository->findWithoutFail($id);

        if (empty($group)) {
            return $this->sendError('Group not found');
        }

        $group->delete();

        return $this->sendResponse($id, trans('group.message_deleted_successfully'));
    }
}
