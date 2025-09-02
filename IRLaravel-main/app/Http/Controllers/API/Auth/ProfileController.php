<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\UpdateProfileAPIRequest;
use App\Http\Requests\ChangeAvatarRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Exception;

/**
 * Class UserController
 * @package App\Http\Controllers\API\Auth
 */
class ProfileController extends AppBaseController
{
    /** @var UserRepository $userRepository */
    protected $userRepository;

    /**
     * UserAPIController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();

        $this->userRepository = $userRepo;
    }

    /**
     * Display the specified User.
     * GET|HEAD /users/{id}
     *
     * @param int $id User ID.
     * @return Response
     */
    public function show($id = 0)
    {
        /** @var \App\Models\User $user */
        $user = (!empty($id)) ? $this->userRepository->find($id) : Auth::user();
        $result = $user->getFullInfo();

        return $this->sendResponse($result, trans('strings.success'));
    }

    /**
     * Update profile by token.
     *
     * @param \App\Http\Requests\API\UpdateProfileAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(UpdateProfileAPIRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $input = $request->all();
        $changeEmail = false;

        try {
            /*if ($request->has('email')) {
                // When change email
                if($input['email'] != $user->email) {
                    // Store new mail in temporary
                    $input['email_tmp'] = $input['email'];
                    $changeEmail = true;
                }

                // Not allow change email now
                unset($input['email']);
            }*/

            $user = $this->userRepository->update($input, $user->id);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }

        $result = $user->getFullInfo();

        if ($changeEmail) {
            // Send mail
            $emailTemplate = 'auth.emails.change_email';
            $this->dispatch(new \App\Jobs\SendChangeMailConfirmation($user, $emailTemplate, \App::getLocale()));
        }

        return $this->sendResponse($result, trans('user.message_updated_profile_successfully'));
    }

    /**
     * @param ChangeAvatarRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function changeAvatar(ChangeAvatarRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $user = $this->userRepository->changeAvatar($user, $request->file('photo'));
        }

        $data = $user->getFullInfo();

        return $this->sendResponse($data, trans('user.message_change_photo_successfully'));
    }
    public function changeLanguage(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $locale = $request->get('locale');

        $user = $this->userRepository->changeLanguage($user, $locale);

        $data = $user->getFullInfo();

        return $this->sendResponse($data, trans('user.message_change_language_successfully'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function removeAvatar()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user = $this->userRepository->removeAvatar($user);

        $data = $user->getFullInfo();

        return $this->sendResponse($data, trans('user.message_change_photo_successfully'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (empty($user)) {
            return $this->sendError(trans('user.not_found'), 404);
        }

        $user->email_tmp = $user->email;
        $user->email = 'removed-'.$user->id.'@itsready.be';
        $user->save();

        $this->userRepository->delete($user->id);

        return $this->sendResponse($user->refresh(), trans('user.deleted_confirm'));
    }
}
