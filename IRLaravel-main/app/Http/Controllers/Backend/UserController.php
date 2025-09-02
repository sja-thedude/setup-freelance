<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\WorkspaceObject;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;
use App\Facades\Helper;
use Flash;
use Response;

/**
 * Class UserController
 * @package App\Http\Controllers\Backend
 */
class UserController extends BaseController
{
    /** @var UserRepository $userRepository */
    private $userRepository;
    /** @var WorkspaceRepository $workspaceRepository */
    private $workspaceRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepo
     * @param WorkspaceRepository $workspaceRepo
     */
    public function __construct(UserRepository $userRepo, WorkspaceRepository $workspaceRepo)
    {
        parent::__construct();

        $this->userRepository = $userRepo;
        $this->workspaceRepository = $workspaceRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->userRepository->updateManagerExpired();
        $users = $this->userRepository->getAllUsers($request, User::USER_TYPE_ALL, User::PLATFORM_BACKOFFICE, $this->perPage, $this->guard);
        $allManagers = $this->userRepository->getAllUsers($request, User::USER_TYPE_ALL, User::PLATFORM_BACKOFFICE, $this->perPage, $this->guard,true);

        return view($this->guard.'.managers.index', [
            'model' => $users,
            'allManagers' => $allManagers
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function indexUser(Request $request)
    {
        $this->userRepository->updateManagerExpired();
        $users = $this->userRepository->getAllUsers($request, User::USER_TYPE_NORMAL, User::PLATFORM_FRONTEND, $this->perPage, $this->guard);

        return view($this->guard.'.users.index', [
            'model' => $users
        ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $roles = $this->userRepository->getRoleList($request, User::USER_TYPE_ALL, User::PLATFORM_BACKOFFICE);
        $genders = User::genders();
        $workspaces = Helper::getActiveWorkspaces();

        return view($this->guard.'.managers.create', ['model' => ''])
            ->with(compact('roles', 'genders', 'workspaces'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $manager = $this->userRepository->storeManager($input);

        if($request->ajax()) {
            return $this->sendResponse(compact('manager'), trans('messages.user.created_successfully'));
        }

        Flash::success(trans('messages.user.created_successfully'));
        return redirect(route($this->guard.'.managers.index'));
    }

    /**
     * Display the specified User.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function show(Request $request, User $user)
    {
        $roles = $this->userRepository->getRoleList($request, User::USER_TYPE_ALL, User::PLATFORM_BACKOFFICE);
        $genders = User::genders();
        $workspaces = Helper::getActiveWorkspaces();
        $workspaceUsers = WorkspaceObject::active()
            ->where('model', User::class)
            ->where('foreign_key', $user->id)
            ->get();

        return view($this->guard.'.managers.show', ['model' => $user])
            ->with(compact('roles', 'genders', 'workspaces', 'workspaceUsers'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user)
    {
        $me = $this->currentUser;
        // Clear filter workspace
        if ($me->isSuperAdmin()) {
            $workspaceId = 0;
        }

        $roles = $this->userRepository->getRoleList($request, User::USER_TYPE_ALL, User::PLATFORM_BACKOFFICE);
        $genders = User::genders();
        $workspaces = Helper::getActiveWorkspaces();
        $workspaceUsers = WorkspaceObject::active()
            ->where('model', User::class)
            ->where('foreign_key', $user->id)
            ->get();

        return view($this->guard.'.managers.edit', ['model' => $user])
            ->with(compact('roles', 'genders', 'workspaces', 'workspaceUsers'));
    }

    /**
     * Update the specified User in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $id = $user->id;
        $input = $request->all();
        $changeEmail = false;

        if ($request->has('email')) {
            // When change email
            if($input['email'] != $user->email) {
                // Store new mail in temporary
                $input['email_tmp'] = $input['email'];
                $changeEmail = true;
            }

            // Not allow change email now
            unset($input['email']);
        }

        // Save data
        $user = $this->userRepository->update($input, $id);

        Flash::success(trans('messages.admin.updated_successfully'));

        if ($changeEmail) {
            // Send mail
            $this->dispatch(new \App\Jobs\SendChangeMailConfirmation($user));
        }

        return redirect(route($this->guard.'.managers.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $managerId = $request->get('manager_id');
        $this->workspaceRepository->assignAccountManager($id, $managerId);
        $this->userRepository->delete($id);

        return $this->sendResponse(null, trans('manager.deleted_confirm'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyUser($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error(trans('user.not_found'));

            return redirect(route($this->guard . '.users.index'));
        }

        $user->email_tmp = $user->email;
        $user->email = 'removed-'.$user->id.'@itsready.be';
        $user->save();

        $this->userRepository->delete($id);

        $response = array(
            'status' => 'success',
            'message' => trans('user.deleted_confirm')
        );

        return response()->json($response);
    }
    /**
     * Ban or Un-ban user
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function status(Request $request, int $id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error(trans('user.not_found'));

            return redirect(route($this->guard.'.managers.index'));
        }

        $user->active = !$user->active;
        $user->save();

        Flash::success(trans('messages.admin.updated_successfully'));

        return redirect()->back();
    }

    /**
     * Update the specified User in storage.
     *
     * @param Request $request
     *
     * @param string $token
     * @return Response
     */
    public function confirmChangeEmail(Request $request, $token)
    {
        // Decode object
        $data = json_decode(base64_decode($token), true);

        if (empty($data['id'])) {
            Flash::error(trans('messages.user.not_found'));
        }

        /** @var \App\Models\User $user */
        $user = $this->userRepository->findWithoutFail($data['id']);

        if (empty($user)) {
            Flash::error(trans('messages.user.not_found'));
        }

        $user->email = $user->email_tmp;
        $user->email_tmp = null;
        $user->save();

        Flash::success(trans('messages.user.changed_email_successfully'));

        return redirect(route($this->guard.'.user.changedEmailSuccess'));
    }

    /**
     * Display the specified User.
     *
     * @return Response
     */
    public function changedEmailSuccess()
    {
        return view($this->guard.'.auth.emails.changed_email_success');
    }

    /**
     * Display the specified User.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function profile(Request $request)
    {
        $user = $this->currentUser;

        return view($this->guard.'.users.profile', ['model' => $user]);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param Request $request
     * @return Response
     */
    public function editProfile(Request $request)
    {
        $user = $this->currentUser;
        $roles = $this->userRepository->getRoleList($request, User::USER_TYPE_ALL);
        $genders = User::genders();
        $isProfile = true;

        return view($this->guard.'.users.edit_profile', ['model' => $user])
            ->with(compact('roles', 'genders', 'isProfile'));
    }

    /**
     * Update the specified User in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        $user = $this->currentUser;
        $id = $user->id;
        $input = $request->all();
        // Save data
        $user = $this->userRepository->update($input, $id);

        if($request->ajax()) {
            $viewRender = view('layouts.partials.modal_manager_view_profile', compact('user'))->render();
            $editRender = view('layouts.partials.modal_manager_edit_profile', compact('user'))->render();
            return $this->sendResponse(compact('user', 'viewRender', 'editRender'), trans('messages.admin.profile_updated_successfully'));
        }

        Flash::success(trans('messages.admin.profile_updated_successfully'));
        return redirect(route($this->guard.'.users.profile'));
    }

    /**
     * Send invitation
     *
     * @param Request $request
     *
     * @param int $id
     * @return Response
     */
    public function sendInvitation($id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        $this->userRepository->sendInvitation($user);

        return $this->sendResponse(null, trans('manager.sent_invitation'));
    }

    public function updateStatus($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error(trans('user.not_found'));

            return redirect(route($this->guard.'.managers.index'));
        }

        $user->active = !$user->active;
        $user->save();

        $response = array(
            'data' => $user->toArray(),
            'status' => $user->active,
        );

        return response()->json($response);
    }
}
