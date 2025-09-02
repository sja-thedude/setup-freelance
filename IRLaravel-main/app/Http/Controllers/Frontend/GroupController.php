<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\GroupRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupController extends BaseController
{
    /**
     * @var GroupRepository
     */
    public $groupRepository;

    /**
     * GroupController constructor.
     *
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        GroupRepository $groupRepository
    ) {
        parent::__construct();

        $this->groupRepository = $groupRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $results = array();

        $request->request->add([
            'workspace_id' => $request->workspaceId,
            'keyword'      => $request->term,
        ]);

        $groups = $this->groupRepository->paginate(100000000);

        foreach ($groups as $item) {
            $closeTimeParese = Carbon::parse($item->close_time);
            $results[] = [
                'id'         => $item->id,
                'value'      => $item->name,
                'close_time' => trans('cart.bestel_tot', [
                    "gio"  => $closeTimeParese->format('H'),
                    "phut" => $closeTimeParese->format('i'),
                ]),
                'type'       => $item->type,
            ];
        }

        return \Response::json($results);
    }
}