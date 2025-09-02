<?php
namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateGroupRestaurantRequest;
use App\Http\Requests\UpdateGroupRestaurantRequest;
use App\Models\GroupRestaurantWorkspace;
use App\Repositories\GroupRestaurantRepository;
use App\Repositories\WorkspaceRepository;
use Flash;
use Illuminate\Http\Request;
use Log;

class GroupRestaurantController extends BaseController
{
    protected $groupRestaurantRepository;
    protected $workspaceRepository;

    public function __construct(
        GroupRestaurantRepository $groupRestaurantRepository,
        WorkspaceRepository $workspaceRepository
    ){
        parent::__construct();
        $this->groupRestaurantRepository = $groupRestaurantRepository;
        $this->workspaceRepository = $workspaceRepository;
    }

    public function index(Request $request)
    {
        $restaurants = $this->workspaceRepository->all()->pluck('name', 'id')->toArray();
        $model = $this->groupRestaurantRepository->getList($request, $this->perPage);

        return view($this->guard . '.grouprestaurant.index')
            ->with([
                'model' => $model,
                'restaurants' => $restaurants
            ]);
    }

    public function create()
    {

    }

    public function store(CreateGroupRestaurantRequest $request)
    {
        $input = $request->all();

        if (!empty($request->logo)) {
            $input['files']['file'][] = $request->logo;
        }

        $input['active'] = 1;

        \DB::beginTransaction();
        try {
            $groupRestaurant = $this->groupRestaurantRepository->create($input);
            //sync categories
            if((isset($input['restaurants']) && !empty($input['restaurants'])) || ($groupRestaurant->groupRestaurantWorkspaces->count() > 0)) {
                GroupRestaurantWorkspace::syncWorkspaces($groupRestaurant, $input['restaurants']);
            }
            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($groupRestaurant, trans('workspace.created_confirm'));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error(__FILE__ . ' - ' . $e->getMessage());

            return $this->sendError($e->getMessage(), 400);
        }

        return redirect(route($this->guard . '.grouprestaurant.index'));
    }

    /**
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateStatus(int $id, Request $request)
    {
        $groupRestaurant = $this->groupRestaurantRepository->findWithoutFail($id);

        if (empty($groupRestaurant)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route($this->guard . '.grouprestaurant.index'));
        }

        //Active - inactive status
        $input['active'] = (int)$request->status;
        $data = $this->groupRestaurantRepository->update($input, $id);

        $response = array(
            //return status result from db
            'data' => $data,
            'status' => $data->active,
        );

        return response()->json($response);
    }

    public function update($id, UpdateGroupRestaurantRequest $request)
    {
        $groupRestaurant = $this->groupRestaurantRepository->findWithoutFail($id);

        if (empty($groupRestaurant)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route('admin.grouprestaurant.index'));
        }

        $input = $request->all();
        if (!empty($request->logo)) {
            $input['files']['file'][] = $request->logo;
        }

        $input['active'] = 1;

        \DB::beginTransaction();
        try {
            $groupRestaurant = $this->groupRestaurantRepository->updateGroupRestaurant($input, $id);
            //sync categories
            if((isset($input['restaurants']) && !empty($input['restaurants'])) || ($groupRestaurant->groupRestaurantWorkspaces->count() > 0)) {
                GroupRestaurantWorkspace::syncWorkspaces($groupRestaurant, $input['restaurants']);
            }
            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($groupRestaurant, trans('workspace.updated_confirm'));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error(__FILE__ . ' - ' . $e->getMessage());

            return $this->sendError($e->getMessage(), 400);
        }

        return redirect(route($this->guard . '.grouprestaurant.index'));
    }

    public function destroy($id)
    {
        $this->groupRestaurantRepository->delete($id);

        $response = array(
            'status' => 'success',
            'message' => trans('grouprestaurant.deleted_confirm')
        );

        return response()->json($response);
    }
}