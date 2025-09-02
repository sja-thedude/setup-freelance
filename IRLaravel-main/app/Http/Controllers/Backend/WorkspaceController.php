<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Helper;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Country;
use App\Models\RestaurantCategory;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceCategory;
use App\Repositories\SettingTimeslotRepository;
use App\Repositories\WorkspaceRepository;
use App\Repositories\UserRepository;
use App\Repositories\SettingOpenHourRepository;
use App\Repositories\SettingGeneralRepository;
use App\Repositories\SettingPreferenceRepository;
use App\Repositories\SettingPaymentRepository;
use App\Repositories\SettingDeliveryConditionsRepository;
use App\Repositories\SettingPrintRepository;
use App\Models\Role;
use Illuminate\Http\Request;
use Flash;
use Auth;
use Response;
use DB;
use Log;
use Illuminate\Http\RedirectResponse;

class WorkspaceController extends BaseController
{
    /** @var WorkspaceRepository $workspaceRepository */
    private $workspaceRepository;
    private $userRepository;
    private $settingOpenHourRepository;
    private $settingTimeslotRepository;
    private $settingGeneralRepository;
    private $settingPreferenceRepository;
    private $settingPaymentRepository;
    private $settingDeliveryConditionsRepository;
    private $settingPrintRepository;

    /**
     * WorkspaceController constructor.
     * @param WorkspaceRepository $workspaceRepo
     * @param UserRepository $userRepos
     * @param SettingOpenHourRepository $settingOpenHourRepo
     * @param SettingTimeslotRepository $settingTimeslotRepo
     * @param SettingGeneralRepository $settingGeneralRepo
     * @param SettingPreferenceRepository $settingPreferenceRepo
     * @param SettingPaymentRepository $settingPaymentRepo
     * @param SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo
     * @param SettingPrintRepository $settingPrintRepo
     */
    public function __construct(
        WorkspaceRepository $workspaceRepo,
        UserRepository $userRepos,
        SettingOpenHourRepository $settingOpenHourRepo,
        SettingTimeslotRepository $settingTimeslotRepo,
        SettingGeneralRepository $settingGeneralRepo,
        SettingPreferenceRepository $settingPreferenceRepo,
        SettingPaymentRepository $settingPaymentRepo,
        SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo,
        SettingPrintRepository $settingPrintRepo
    ) {
        parent::__construct();

        $this->workspaceRepository = $workspaceRepo;
        $this->userRepository = $userRepos;
        $this->settingOpenHourRepository = $settingOpenHourRepo;
        $this->settingTimeslotRepository = $settingTimeslotRepo;
        $this->settingGeneralRepository = $settingGeneralRepo;
        $this->settingPreferenceRepository = $settingPreferenceRepo;
        $this->settingPaymentRepository = $settingPaymentRepo;
        $this->settingDeliveryConditionsRepository = $settingDeliveryConditionsRepo;
        $this->settingPrintRepository = $settingPrintRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $workspaces = $this->workspaceRepository->getLists($request, $this->currentUser, $this->perPage);
        $accountManagers = User::where('platform', User::PLATFORM_BACKOFFICE)
            ->where('is_super_admin', '!=',  User::SUPER_ADMIN_ID)
            ->where('is_super_admin', false)
            ->pluck('name', 'id')->toArray();
        $languages = Helper::getActiveLanguages();
        $types = RestaurantCategory::getAll();
        $countries = Country::getCountriesList();

        return view($this->guard . '.workspaces.index')
            ->with(compact(
                'workspaces', 
                'accountManagers', 
                'languages', 
                'types',
                'countries'
            ));
    }

    /**
     * Show the form for creating a new Workspace.
     *
     * @return Response
     */
    public function create()
    {
        // Shipment fulfillment options
        $fulfillmentOptions = $this->workspaceRepository->getFulfillmentOptions();

        return view('admin.workspaces.create')
            ->with(compact('fulfillmentOptions'));
    }

    /**
     * @param CreateWorkspaceRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(CreateWorkspaceRequest $request)
    {
        $input = $request->all();

        $input['active'] = isset($input['active']) && !empty($input['active']) ? Workspace::ACTIVE : Workspace::INACTIVE;
        $input['status'] = Workspace::STATUS_INVITATION_SENT;
        if (!empty($request->uploadAvatar)) {
            $input['files']['file'][] = $request->uploadAvatar;
        }
        if (!isset($input['active_languages'])) {
            $input['active_languages'] = ['nl'];
        }

        DB::beginTransaction();
        try {
            $workspace = $this->workspaceRepository->create($input);
            $user = $this->userRepository->storeUserWorkspace($workspace);
            
            //Save user id
            $workspace->user_id = $user->id;
            $workspace->save();
            
            //sync categories
            if((isset($input['types']) && !empty($input['types'])) || !empty($workspace->workspaceCategories)) {
                WorkspaceCategory::syncCategories($workspace, $input['types']);
            }

            $this->settingOpenHourRepository->initOpenHourForWorkspace($workspace->id);
            $this->settingTimeslotRepository->initTimeSlotForWorkspace($workspace->id);
            $this->settingGeneralRepository->initSettingGeneralForWorkspace($workspace->id);
            $this->settingPreferenceRepository->initSettingPreferenceForWorkspace($workspace->id);
            $this->settingPaymentRepository->initSettingPaymentForWorkspace($workspace->id);
            $this->settingDeliveryConditionsRepository->initSettingDeliveryConditionsForWorkspace($workspace->id);
            $this->settingPrintRepository->initSettingPrintForWorkspace($workspace->id);

            DB::commit();
            
            if($request->ajax()) {
                return $this->sendResponse($workspace, trans('workspace.created_confirm'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(__FILE__ . ' - ' . $e->getMessage());
            
            return $this->sendError(null, 400, $e->getMessage());
        }
        
        Flash::success(trans('workspace.created_successfully'));

        return redirect(route('admin.workspaces.index'));
    }

    /**
     * Display the specified Workspace.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route('admin.workspaces.index'));
        }

        return view('admin.workspaces.show')->with('workspace', $workspace);
    }

    /**
     * Show the form for editing the specified Workspace.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route('admin.workspaces.index'));
        }

        return view('admin.workspaces.edit')
            ->with('workspace', $workspace);
    }

    /**
     * @param UpdateWorkspaceRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateWorkspaceRequest $request, $id)
    {
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route('admin.workspaces.index'));
        }
        
        $input = $request->all();
        if (!isset($input['active_languages'])) {
            $input['active_languages'] = ['nl'];
        }
        if (!empty($request->uploadAvatar)) {
            $input['files']['file'][] = $request->uploadAvatar;
        }
        
        DB::beginTransaction();
        try {
            $workspace = $this->workspaceRepository->updateWorkspace($input, $id);
            
            $this->userRepository->syncWorkspaceInfoToUser($request, $workspace->user, $workspace);
            
            //sync categories
            if((isset($input['types']) && !empty($input['types'])) || !empty($workspace->workspaceCategories)) {
                WorkspaceCategory::syncCategories($workspace, $input['types']);
            }

            DB::commit();
            
            if($request->ajax()) {
                return $this->sendResponse($workspace, trans('workspace.updated_confirm'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(__FILE__ . ' - ' . $e->getMessage());
            
            return $this->sendError(null, 400, $e->getMessage());
        }

        return redirect(route('admin.workspaces.index'));
    }

    /**
     * Remove the specified Workspace from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /*$workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route($this->guard . '.workspaces.index'));
        }*/

        $this->workspaceRepository->forceDelete($id);

        $response = array(
            'status' => 'success',
            'message' => trans('workspace.deleted_confirm')
        );

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetRoles(Request $request)
    {
        /** @var \App\Models\Role $roleInstance */
        $workspaceId = (int)$request->get('id');
        $roleInstance = Role::getInstance();
        $roles = $roleInstance->withWorkspace($workspaceId)
            ->select('roles.' . $roleInstance->getKeyName(), 'roles.name')
            ->get();

        return $this->sendResponse($roles, trans('strings.success'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateStatus(int $id, Request $request)
    {
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route($this->guard . '.workspaces.index'));
        }

        //Active - inactive status
        $input['active'] = (int)$request->status;
        $data = $this->workspaceRepository->update($input, $id);

        $response = array(
            //return status result from db
            'data' => $data,
            'status' => !empty($request->is_online) ? $data->is_online : $data->active,
        );

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $workspaceId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function autoLogin(Request $request, int $id, int $workspaceId)
    {
        $user = $this->userRepository->find($id);
        Auth::guard('manager')->login($user);

        $workspace = $this->workspaceRepository->find($workspaceId);
        $userLogin = $user;
        $userLogin->workspace_id = $workspaceId;
        $userLogin->load('workspace');
        $locale = $workspace->language;

        session(['auth_temp' => $userLogin]);
        
        // Push locale setting of user to session
        if (!empty($locale)) {
            session(['locale' => $locale]);
        }

        // Redirect back URL
        $referer = route(config('module.manager') . '.login');
        $path = trim(str_replace(url('/'), '', $referer), '/');
        $params = explode('/', $path);

        if (count($params) > 0 && app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
            if ($locale && app('laravellocalization')->checkLocaleInSupportedLocales($locale)
                && !(app('laravellocalization')->getDefaultLocale() === $locale && app('laravellocalization')->hideDefaultLocaleInURL())) {
                $redirection = app('laravellocalization')->getLocalizedURL($locale, $referer);
                return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
            }
        }

        return redirect(route(config('module.manager') . '.login'));
    }

    /**
     * @param Request $request
     * @param $ids
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function assignAccountManager(Request $request, $ids) {
        $managerId = $request->get('manager_id');
        $arrIds = explode(',', $ids);
        
        if (!empty($arrIds) && $ids != 0) {
            $this->workspaceRepository->assignAccountManagerToWorkspaces($arrIds, $managerId);
            return $this->sendResponse(null, count($arrIds) > 1 ? trans('workspace.updated_assign_confirms') : trans('workspace.updated_assign_confirm'));
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('workspace.not_found_assign'),
                'data' => null
            ]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvitation($id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        $this->workspaceRepository->sendInvitation($user);

        return $this->sendResponse(null, trans('manager.sent_invitation'));
    }
}
