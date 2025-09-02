<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Backend\BaseController;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Carbon;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Flash;
use Log;
use Response;

/**
 * Class ProfileController
 * @package App\Http\Controllers\Auth
 */
class ProfileController extends BaseController
{
    /** @var UserRepository $userRepository */
    protected $userRepository;

    /**
     * ProfileController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();

        $this->userRepository = $userRepo;
    }

    /**
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $userId = auth()->user()->id;

        try {
            \DB::beginTransaction();

            $data = $request->all();
            if (isset($request->uploadAvatar)) {
                $path = $this->userRepository->uploadFile($request->uploadAvatar);
                $data['photo'] = url($path);
            }

            if ($request->has('deleteAvatar')) {
                $data['photo'] = NULL;
            }

            if (!empty($data['birthday'])) {
                $data['birthday'] = Carbon::parse(str_replace('/', '-', $data['birthday']))->format('Y-m-d');
            }

            $user = $this->userRepository->update($data, $userId);

            \DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($user->toArray(), trans('Update profile success'));
            }

            Flash::success('Update profile success');

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
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

        return redirect(route('user.changedEmailSuccess'));
    }

    /**
     * Display the specified User.
     *
     * @return Response
     */
    public function changedEmailSuccess()
    {
        return view('auth.changed_email_success');
    }

}
