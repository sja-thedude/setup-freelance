<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Helpers\Helper;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendEmail;
use App\Jobs\SendChangeMailConfirmation;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'password',
        'photo',
        'description',
        'is_super_admin',
        'is_admin',
        'remember_token',
        'created_at',
        'updated_at',
        'role_id',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @param Request $request
     * @param int $userType
     * @param int $platform
     * @param int $perPage
     * @param string $guard
     * @param bool $getAll
     * @param bool $workspaceId
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getAllUsers(Request $request, $userType = 0, $platform = 0, $perPage = 10, $guard = 'admin', $getAll = false, $workspaceId = false)
    {
        /** @var \App\Models\User $me */
        $me = Auth::guard($guard)->user();

        $this->scopeQuery(function (Model $model) use ($request, $me, $userType, $platform, $getAll, $workspaceId) {
            // Relation
            $model = $model->with(['role']);
            //Sort
            $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
            $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';
            $model = $model->orderBy($sortBy, $orderBy);

            if (array_keys(User::getPlatforms(), $platform)) {
                $model = $model->where('users.platform', $platform);
            }
            
            //Get user order by workspace and user_id | using for manager user
            if ($workspaceId) {
                $model = $model->join('orders', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw('users.*, orders.user_id, MAX(orders.created_at) as last_order_date'))
                    ->where('orders.workspace_id', $workspaceId)
                    ->groupBy('user_id');
            }

            // Filter by type (is admin or not)
            if (in_array($userType, [User::USER_TYPE_ADMIN, User::USER_TYPE_NORMAL])) {
                $model = $model->where('users.is_admin', ($userType == User::USER_TYPE_ADMIN) ? User::IS_YES : User::IS_NO);
            }

            // Except me
            if(empty($getAll)) {
                $model = $model->whereNotIn('users.id', [$me->is_super_admin, User::SUPER_ADMIN_ID])
                    ->where('users.is_super_admin', false);
            }
            
            // Search by keyword
            if ($request->has('keyword') && $request->get('keyword') != '') {
                $keyword = $request->get('keyword');

                $model = $model->where(function ($query) use ($keyword) {
                    $query->where('users.name', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('users.email', 'LIKE', '%' . $keyword . '%');
                });
            }

            // Filter by role
            if ($request->has('role_id') && $request->get('role_id') != '') {
                $model = $model->where('users.role_id', (int)$request->get('role_id'));
            }

            // Filter by active status
            if ($request->has('active') && $request->get('active') != '') {
                $model = $model->where('users.active', (int)$request->get('active'));
            }

            // Filter by verify status
            if ($request->has('is_verified') && $request->get('is_verified') != '') {
                $model = $model->where('users.is_verified', (int)$request->get('is_verified'));
            }

            return $model;
        });

        $this->pushCriteria(new RequestCriteria($request));
        
        if(empty($getAll)) {
            $users = $this->paginate($perPage);
        } else {
            $users = $this->all();
        }

        return $users;
    }

    /**
     * Get role list
     *
     * @param Request $request
     * @param int $userType User type. 0: User, 1: Admin.
     * @return \Illuminate\Support\Collection
     */
    public function getRoleList(Request $request, $userType = 0, $platform = 0)
    {
        /** @var \App\Models\Role $roleInstance */
        $roleInstance = Role::getInstance();
        $roles = Role::where('roles.active', Role::IS_YES);
        $roles->where('roles.platform', $platform);

        // With User List, Role = User
        if ($userType == User::USER_TYPE_ADMIN) {
            // Other admin roles
            $roles->whereNotIn('roles.' . $roleInstance->getKeyName(), $roleInstance->getNormalUserRoles());
        } else if ($userType == User::USER_TYPE_NORMAL) {
            // User
            $roles->whereIn('roles.' . $roleInstance->getKeyName(), $roleInstance->getNormalUserRoles());
        }

        // Get list
        /** @var \Illuminate\Support\Collection $roles */
        $roles = $roles->pluck('roles.name', 'roles.' . $roleInstance->getKeyName());
        // Translate
        $roles->transform(function ($item, $key) {
            return Helper::trans($item, [], 'role');
        });

        return $roles;
    }

    /**
     * Attach multi workspaces and roles
     *
     * @param int $userId
     * @param array $data
     * @return array|null
     */
    public function attachWorkspaces(int $userId, array $data)
    {
        $response = null;

        if (count($data) > 0) {
            /** @var \App\Models\WorkspaceObject $workspaceObject */
            $workspaceObject = \App\Models\WorkspaceObject::getInstance();
            /** @var \App\Models\Role $roleInstance */
            $roleInstance = Role::getInstance();
            $response = [];

            // Attach workspaces
            foreach ($data as $item) {
                $metaData = [
                    'role_id' => $item['role_id'],
                    'is_admin' => in_array($item['role_id'], $roleInstance->getAdminRoles()),
                ];
                $response[] = $workspaceObject->attachObject($this->model(), $userId, $item['workspace_id'], $metaData);
            }
        }

        return $response;
    }

    /**
     * Attach multi workspaces and roles
     *
     * @param int $userId
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function reloadWorkspaces(int $userId, array $data)
    {
        $response = null;

        if (count($data) > 0) {
            /** @var \App\Models\WorkspaceObject $workspaceObject */
            $workspaceObject = \App\Models\WorkspaceObject::getInstance();

            // Detach exist workspaces
            $workspaceObject->detachObject($this->model(), $userId);

            // Attach workspaces
            $response = $this->attachWorkspaces($userId, $data);
        }

        return $response;
    }

    /**
     * Overwrite create function from base
     *
     * @param array $attributes
     * @return \App\Models\User
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function create(array $attributes)
    {
        // Begin transaction
        DB::beginTransaction();

        // Random password
        $password = User::randomPassword();
        $attributes['default_password'] = $password;
        $attributes['password'] = bcrypt($password);

        // Create new user
        $user = parent::create($attributes);

        // Attach workspace if set
        if (isset($attributes['workspaces'])) {
            // Assign user to workspace
            $this->attachWorkspaces($user->id, $attributes['workspaces']);
        }

        // Commit transaction
        DB::commit();

        // Send mail
        dispatch(new \App\Jobs\SendAdminPasswordEmail($user));

        return $user;
    }

    /**
     * Overwrite update function from base
     *
     * @param array $attributes
     * @param int $id
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $attributes, $id)
    {
        // Name = First name + Last name
        if (!array_key_exists('name', $attributes)) {
            $attributes['name'] = trim(array_get($attributes, 'first_name', '')
                . ' ' . array_get($attributes, 'last_name', ''));
        }

        // Update user data
        $user = parent::update($attributes, $id);

        // Attach workspace if set
        if (isset($attributes['workspaces'])) {
            // Reload workspace roles
            $this->reloadWorkspaces($user->id, $attributes['workspaces']);
        }

        return $user;
    }

    /**
     * @param Request $request
     * @param int $userType User type. 0: User, 1: Admin.
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkUserBan($email = '')
    {
        $user = $this->makeModel()->where('email', $email)->first();

        if(!empty($user) && empty($user->deleted_at) && empty($user->active)) {
            return false;
        }

        return true;
    }
    
    public function sendInvitation($user) {
        $newPassword = Str::random(6);
        $user->password = Hash::make($newPassword);
        $user->default_password = $newPassword;
        $user->setRememberToken(Str::random(60));
        $user->status = User::STATUS_INVITATION_SENT;
        $user->is_verified = false;
        $user->verify_expired_at = date(config('datetime.dateTimeDb'), strtotime('+1 day', strtotime(now())));
        $user->save();

        dispatch(new SendEmail([
            'template' => 'emails.invitation',
            'user' => $user,
            'link' => route('admin.showlogin'),
            'newPassword' => $newPassword,
            'subject' => trans('manager.send_invitation_subject'),
        ], $user->getLocale()));
    }

    public function updateManagerExpired() {
        return $this->makeModel()
                ->where('platform', User::PLATFORM_BACKOFFICE)
                ->whereNotNull('verify_expired_at')
                ->where('verify_expired_at', '<', date(config('datetime.dateTimeDb')))
                ->update([
                    'verify_expired_at' => null,
                    'status' => User::STATUS_INVITATION_EXPIRED
                ]);
    }
    
    public function storeManager($input) {
        $newPassword = Str::random(6);
        $user = $this->makeModel()->create([
            'role_id' => Role::ROLE_ADMIN,
            'email' => $input['email'],
            'gsm' => $input['gsm'],
            'password' => Hash::make($newPassword),
            'default_password' => $newPassword,
            'is_super_admin' => false,
            'is_admin' => true,
            'active' => true,
            'is_verified' => false,
            'verify_expired_at' => date(config('datetime.dateTimeDb'), strtotime('+1 day', strtotime(now()))),
            'platform' => User::PLATFORM_BACKOFFICE,
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'status' => User::STATUS_INVITATION_SENT
        ]);

        $user->setRememberToken(Str::random(60));
        $user->save();

        dispatch(new SendEmail([
            'template' => 'emails.invitation',
            'user' => $user,
            'link' => route('admin.showlogin'),
            'newPassword' => $newPassword,
            'subject' => trans('manager.send_invitation_subject'),
        ], $user->getLocale()));
        
        return $user;
    }

    /**
     * Change avatar of User
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile $file
     * @return User
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function changeAvatar(User $user, UploadedFile $file)
    {
        $path = null;

        if (!empty($file)) {
            $path = $this->uploadFile($file);

            if (!empty($path)) {
                $user->photo = $path;
            }
        }

        // Save
        $user->save();

        return $user;
    }

    /**
     * Remove avatar from User
     *
     * @param User $user
     * @return User
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function removeAvatar(User $user)
    {
        if (!empty($user->photo)) {
            $path = 'public/' . $user->getOriginal('photo');

            // When we edit a product/category and we upload the new picture to it,
            // we must keep the old picture,
            // so it will not impact the other restaurants
            // which are using the data imported from that restaurant.
            /*if (Storage::exists($path)) {
                Storage::delete($path);
            }*/
        }

        $user->photo = null;
        // Save
        $user->save();

        return $user;
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function storeUserWorkspace($data) {
        $newPassword = Str::random(6);
        $firstName = $data->manager_name;
        $lastName = $data->surname; 
        $name = $firstName ." ".$lastName;
        $user = $this->makeModel()->create([
            'role_id' => Role::ROLE_MANAGER,
            'email' => $data->email,
            'password' => Hash::make($newPassword),
            'default_password' => $newPassword,
            'is_super_admin' => false,
            'is_admin' => true,
            'active' => true,
            'is_verified' => true,
            'verify_expired_at' => null,
            'platform' => User::PLATFORM_MANAGER,
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'status' => User::STATUS_ACTIVE,
            'gsm' => $data->gsm,
            'address' => $data->address,
            'locale' => $data->language,
            'workspace_id' => $data->id,
        ]);

        $user->setRememberToken(Str::random(60));
        $user->save();

        dispatch(new SendEmail([
            'template' => 'emails.workspace_invitation',
            'user' => $user,
            'link' => route('manager.showlogin'),
            'newPassword' => $newPassword,
            'subject' => trans('manager.send_invitation_subject'),
        ], $user->getLocale()));
        
        return $user;
    }

    /**
     * @param $user
     * @param $route
     * @return mixed
     */
    public function forgotPassword($user, $route) {
        $newPassword = Str::random(6);
        $user->verify_expired_at = date(config('datetime.dateTimeDb'), strtotime('+1 day', strtotime(now())));
        $user->password_tmp = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        $user->save();

        $locale = $user->getLocale();
        app()->setLocale($locale);
        dispatch(new SendEmail([
            'template' => 'emails.forgot_password',
            'user' => $user,
            'link' => $route,
            'newPassword' => $newPassword,
            'subject' => trans('auth.forgot_password_subject'),
        ], $locale));

        return $user;
    }

    /**
     * @param $request
     * @param $user
     * @param $workspace
     * @return mixed
     */
    public function syncWorkspaceInfoToUser($request, $user, $workspace) {
        $changeEmail = false;
        
        if ($request->has('email')) {
            // When change email
            if($request->email != $user->email) {
                // Store new mail in temporary
                $user->email_tmp = $request->email;
                $changeEmail = true;
            }
        }
        
        $user->first_name = $workspace->manager_name;
        $user->last_name = $workspace->surname;
        $user->email = $workspace->email;
        $user->gsm = $workspace->gsm;
        $user->locale = $workspace->language;
        $user->save();
        
//        if ($changeEmail) {
//            // Send mail
//            dispatch(new SendChangeMailConfirmation($user));
//        }
        
        return $user;
    }
    public function changeLanguage(User $user, string $locale)
    {
        $user->locale = $locale;
        $user->save();

        return $user;
    }
}
