<?php

namespace App\Http\Controllers\Manager;

use App\Models\Country;
use App\Models\Notification;
use App\Models\Reward;
use App\Models\User;
use App\Models\Post;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Repositories\LoyaltyRepository;
use Illuminate\Http\Request;
use Flash;
use App\Repositories\NotificationRepository;
use App\Repositories\NotificationPlanRepository;
use App\Repositories\WorkspaceRepository;

/**
 * Class UserController
 * @package App\Http\Controllers\Manager
 */
class UserController extends BaseController
{
    /** @var UserRepository $userRepository */
    private $userRepository;
    private $loyaltyRepository;
    private $notificationRepository;
    private $notificationPlanRepository;
    private $workspaceRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepo
     * @param LoyaltyRepository $loyaltyRepo
     * @param NotificationRepository $notificationRepo
     * @param NotificationPlanRepository $notificationPlanRepo
     */
    public function __construct(
        UserRepository $userRepo,
        LoyaltyRepository $loyaltyRepo,
        NotificationRepository $notificationRepo,
        NotificationPlanRepository $notificationPlanRepo,
        WorkspaceRepository $workspaceRepo
    )
    {
        parent::__construct();

        $this->userRepository = $userRepo;
        $this->loyaltyRepository = $loyaltyRepo;
        $this->notificationRepository = $notificationRepo;
        $this->notificationPlanRepository = $notificationPlanRepo;
        $this->workspaceRepository = $workspaceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->userRepository->updateManagerExpired();
        $userType = -1; // User::USER_TYPE_NORMAL
        $platform = -1; // User::PLATFORM_FRONTEND
        $model = $this->userRepository->getAllUsers($request, $userType, $platform, $this->perPage, $this->guard, false, $this->tmpUser->workspace_id);
        $listReward = Reward::getRewardMax($this->tmpWorkspace->id);
        $rewardMax = $listReward->first();

        return view($this->guard.'.users.index', compact(
            'model',
            'rewardMax',
            'listReward'
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changedEmailSuccess()
    {
        return view($this->guard.'.auth.emails.changed_email_success');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $user = $this->currentUser;
        $id = $user->id;
        $post = Post::where('post_author', $id)
            ->where('post_type', 'post')
            ->orderby('id', 'desc')
            ->get();

        return view($this->guard.'.users.profile', ['model' => $user, 'post' => $post]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProfile(Request $request)
    {
        $user = $this->currentUser;
        $roles = $this->userRepository->getRoleList($request, User::USER_TYPE_ALL);
        $genders = User::genders();
        $isProfile = true;
        $countries = Country::getCountryList();

        return view($this->guard.'.users.edit_profile', ['model' => $user])
            ->with(compact('roles', 'genders', 'isProfile', 'countries'));
    }

    /**
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        $user = $this->currentUser;
        $id = $user->id;
        $input = $request->all();
        // Save data
        $user = $this->userRepository->update($input, $id);

        //Update workspace if isset
        if (!empty($user->workspace)) {
            $input = array_merge($input, [
                'manager_name' => $input['first_name'],
                'surname' => $input['last_name'],
                'facebook_enabled' => $user->workspace->facebook_enabled,
                'google_enabled' => $user->workspace->google_enabled,
                'apple_enabled' => $user->workspace->apple_enabled,
            ]);
            $this->workspaceRepository->updateWorkspace($input, $user->workspace->id);
        }

        if($request->ajax()) {
            $viewRender = view('layouts.partials.modal_manager_view_profile', compact('user'))->render();
            $editRender = view('layouts.partials.modal_manager_edit_profile', compact('user'))->render();
            return $this->sendResponse(compact('user', 'viewRender', 'editRender'), trans('messages.admin.profile_updated_successfully'));
        }

        Flash::success(trans('messages.admin.profile_updated_successfully'));
        return redirect(route($this->guard.'.users.profile'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateCredit(Request $request, $id) {
        $input = $request->all();

        $data = $this->loyaltyRepository->updateOrCreate(
            [
                'workspace_id' => $this->tmpWorkspace->id,
                'user_id' => $id,
            ],
            $input
        );

        //Push notification when reaching 100% of credits
        if ($request->point >= $request->max_point) {
            $input['send_datetime'] = $input['sent_time'];
            $input['platform'] = Notification::MANAGER;
            $input['workspace_id'] = $this->tmpWorkspace->id;
            $input['gender_dest_male'] = !empty($request->gender_dest_male) ? Notification::CHECKED_GENDER : Notification::UNCHECKED_GENDER;
            $input['gender_dest_female'] = !empty($request->gender_dest_female) ? Notification::CHECKED_GENDER : Notification::UNCHECKED_GENDER;
            $input['description'] = trans('user.message_for_user_credit');

            /** @var \App\Models\NotificationPlan $notificationPlan */
            $notificationPlan = $this->notificationPlanRepository->create($input);

            if (empty($notificationPlan) || empty($notificationPlan->id)) {
                throw new \Exception('Unable to create new Notification Plan');
            }

            // Apply notification to users
            $input['notification_plan_id'] = $notificationPlan->id;
            $input['template_id'] = $notificationPlan->workspace_id;
            $this->notificationRepository->applyNotification($input);

            // Mark is send plan
            $notificationPlan->is_sent = true;
            $notificationPlan->save();
        }

        return $this->sendResponse($data->toArray(), trans('messages.admin.profile_updated_successfully'));
    }
}
