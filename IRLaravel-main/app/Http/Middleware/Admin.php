<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Facades\Helper;

class Admin
{
    private $except = [
        'App\Http\Controllers\Backend\Auth\AuthController@logout',
        'App\Http\Controllers\Backend\Auth\ChangePasswordController@changePasswordForm',
        'App\Http\Controllers\Backend\Auth\ChangePasswordController@changePassword',
        'App\Http\Controllers\Backend\UserController@profile',
        'App\Http\Controllers\Backend\UserController@editProfile',
        'App\Http\Controllers\Backend\UserController@updateProfile',
        'App\Modules\ContentManager\Controllers\MediaController@index',
        'App\Modules\ContentManager\Controllers\MediaController@store',
        'App\Modules\ContentManager\Controllers\MediaController@images',
        'App\Modules\ContentManager\Controllers\MediaController@destroy',
        'App\Http\Controllers\Backend\ClientController@getPostalCodes',
        'App\Http\Controllers\Backend\NotificationController@store',
        'App\Http\Controllers\Backend\WorkspaceController@assignAccountManager',
        'App\Http\Controllers\Backend\DashboardController@index',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        // User is guest
        if (\Auth::guard($guard)->guest()) {
            return redirect($guard . '/login');
        }

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = \Auth::guard($guard)->user();
        
        // User is super admin
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // When is not admin user
        if (!$user->isAdmin() || $user->platform != User::PLATFORM_BACKOFFICE) {
            if ($user->platform == User::PLATFORM_FRONTEND) {
                $message = 'Coming soon!';
                // Logout before redirect
                \Auth::guard($guard)->logout();
                return redirect()->back()
                    ->withErrors($message);
            }

            return redirect('/admin');
        }

        if(empty($user->active)) {
            return abort(403, json_encode([
                'link' => route($guard.'.login'),
                'user' => $user
            ]));
        }
        
        // Get role from cache if exist
        $guardDetail = ($user->isSuperAdmin() ? 'super_admin' : 'account_manager');
        $roleCacheName = config('cache.key'). $guardDetail;
        $rolePermission = $this->getRolePermission($roleCacheName, $guardDetail);
        
        // Check user has access
        $routeAction = \Route::currentRouteAction();
        $controllerAndAction = $this->requiredPermission($request);
        $route = strtolower(str_replace('Controller', '', $controllerAndAction));
        
        if (empty($controllerAndAction)) {
            return abort(403, json_encode([
                'link' => route($guard.'.login'),
                'user' => $user
            ]));
        }

        if (in_array($route, $rolePermission) || in_array($routeAction, $this->except)) {
            return $next($request);
        }
        
        // Request ajax
        if ($request->ajax()) {
            return response()->json(['status' => 403, 'success' => false, 'message' => 'Unauthorised.'], 403);
        }

        return abort(403, json_encode([
            'link' => route($guard.'.login'),
            'user' => $user
        ]));
    }

    protected function getRolePermission($roleCacheName, $guardDetail) {
        $rolePermission = [];
        
        if (cache()->has($roleCacheName)) {
            $rolePermission = cache($roleCacheName);
        } else {
            $permissionConfig = config('permission');

            foreach($permissionConfig as $permission) {
                if(!empty($permission['actions'])) {
                    $actions = $permission['actions'];
                    foreach($actions as $action) {
                        if(($guardDetail == 'account_manager' && empty($action['only_super_admin']))
                            || $guardDetail == 'super_admin') {
                            $rolePermission[] = $action['action'];
                        } 
                    }
                }
            }

            $rolePermission = $this->_addMorePermissionPosted($rolePermission);
            cache()->forever($roleCacheName, $rolePermission);
        }
        
        return $rolePermission;
    }
    /**
     * Extract required permission from requested route
     *
     * @param  \Illuminate\Http\Request $request
     * @param string $guard
     * @return array permission_slug connected to the Route
     */
    protected function requiredPermission($request, $guard = 'admin')
    {
        $action = $request->route()->getAction();
        $required = null;

        if (isset($action['controller'])) {
            $controller = isset($action['namespace']) ? explode("{$action['namespace']}\\", $action['controller']) : [];

            $required = !empty($controller) ? $controller[1] : $action['controller'];
        }

        return $required;
    }
    
    /**
     * Add more permission to permission posted (Example : if permission is create, need to add store)
     *
     * @param array $permission
     * @return array
     */
    protected function _addMorePermissionPosted($permission)
    {
        $permission[] = 'login@logout';
        $permission[] = 'auth@logout';

        if (!empty($permission)) {
            foreach ($permission as $action) {
                $fieldData = explode('@', $action);
                
                switch ($fieldData[1]) {
                    case 'create':
                        if (!in_array($fieldData[0] . '@store', $permission)) {
                            $permission[] = $fieldData[0] . '@store';
                        }

                        if (!in_array($fieldData[0] . '@index', $permission)) {
                            $permission[] = $fieldData[0] . '@index';
                        }

                        if (!in_array($fieldData[0] . '@show', $permission)) {
                            $permission[] = $fieldData[0] . '@show';
                        }
                        break;
                    case 'edit':
                        if (!in_array($fieldData[0] . '@update', $permission)) {
                            $permission[] = $fieldData[0] . '@update';
                        }

                        if (!in_array($fieldData[0] . '@index', $permission)) {
                            $permission[] = $fieldData[0] . '@index';
                        }

                        if (!in_array($fieldData[0] . '@show', $permission)) {
                            $permission[] = $fieldData[0] . '@show';
                        }
                        break;
                }
            }
        }

        return array_unique($permission);
    }
}
