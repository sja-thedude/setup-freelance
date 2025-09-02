<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserSocial;
use App\Models\Workspace;
use App\Models\GroupRestaurant;
use Socialite;
use JWTAuth, JWTAuthException;

class SocialRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'provider',
        'provider_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return UserSocial::class;
    }

    public function getDriver($provider, $workspaceId = null, $groupId = null) {
        $providers = array_keys(config('services'));
        $pId = $provider .'_id';
        $pKey = $provider .'_key';
        $result = [
            'status' => false,
            'message' => trans('social.provider_invalid')
        ];

        try {
            if(!in_array($provider, $providers)) {
                return $result;
            }

            if($provider == UserSocial::PROVIDER_FACEBOOK) {
                $customProvider = \Laravel\Socialite\Two\FacebookProvider::class;
            } elseif($provider == UserSocial::PROVIDER_GOOGLE) {
                $customProvider = \Laravel\Socialite\Two\GoogleProvider::class;
            } else {
                $customProvider = \App\Providers\SocialLoginAppleProvider::class;
            }

            $driver = null;
            $redirectUrl = config('services.'. $provider .'.redirect');

            if(!empty($groupId)) {
                $group = GroupRestaurant::findOrFail($groupId);
                $redirectUrl = url('login/social/callback/'.$provider);

                if(!(empty($group) || empty($group->$pId) || empty($group->$pKey))) {
                    $driver = Socialite::buildProvider($customProvider, [
                        'client_id' => $group->$pId,
                        'client_secret' => $group->$pKey,
                        'redirect' => $redirectUrl
                    ]);
                }
            } elseif(!empty($workspaceId)) {
                $workspace = Workspace::findOrFail($workspaceId);
                $redirectUrl = url('login/social/callback/'.$provider);

                if(!(empty($workspace) || empty($workspace->$pId) || empty($workspace->$pKey))) {
                    $driver = Socialite::buildProvider($customProvider, [
                        'client_id' => $workspace->$pId,
                        'client_secret' => $workspace->$pKey,
                        'redirect' => $redirectUrl
                    ]);
                }
            }

            if(is_null($driver)) {
                $driver = Socialite::buildProvider($customProvider, [
                    'client_id' => config('services.'. $provider .'.client_id'),
                    'client_secret' => config('services.'. $provider .'.client_secret'),
                    'redirect' => $redirectUrl
                ]);
            }

            unset($result['message']);
            $result['status'] = true;
            $result['driver'] = $driver;

            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    public function syncSocialUser($request, $provider, $socialUser) {
        if (isset($socialUser->user['gender']) && $socialUser->user['gender'] == 'male'){
            $gender = User::GENDER_FEMALE;
        } elseif (isset($socialUser->user['gender']) && $socialUser->user['gender'] == 'male'){
            $gender = User::GENDER_MALE;
        } else {
            $gender = User::GENDER_OTHER;
        }

        // Check exist user with email
        if(!empty($socialUser->getEmail())) {
            $user = User::where('email', $socialUser->getEmail())
                ->where('is_admin', false)
                ->first();
        }

        // If not exist email, check with provider
        if(empty($user)) {
            $user = User::where('is_admin', false)
                ->whereHas('social', function ($query) use ($provider, $socialUser) {
                    $query->where('provider', $provider)
                        ->where('provider_id', $socialUser->getId());
                })
                ->first();
        }

        $firstLogin = false;

        // If still not exit, create new
        if(empty($user)) {
            $name = !empty($socialUser->getName()) ? $socialUser->getName() : $socialUser->getEmail();
            $userData = [
                'role_id' => \App\Models\Role::ROLE_USER,
                'first_name' => $name ?? $socialUser->getId(),
                'name' => $name ?? $socialUser->getId(),
                'email' => $socialUser->getEmail() ?? $socialUser->getId(),
                'photo' => $socialUser->getAvatar(),
                'gender' => $gender,
                'platform' => User::PLATFORM_FRONTEND,
                'status' => 1,
                'is_verified' => 1,
                'api_token' => str_random(60),
                'locale' => $request->header('content-language', 'nl')
            ];

            $user = User::create($userData);
            $firstLogin = true;
        }

        $socialData = [
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId()
        ];

        UserSocial::updateOrCreate($socialData, $socialData);

        return compact('user', 'firstLogin');
    }
}
