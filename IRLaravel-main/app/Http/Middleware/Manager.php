<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Workspace;

class Manager
{
    private $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'manager')
    {
        // User is guest
        if (\Auth::guard($guard)->guest()) {
            return redirect($guard . '/login');
        }

        $user = \Auth::guard($guard)->user();
        $tmpUser = session('auth_temp');

        if (!empty($tmpUser)) {
            $tmpWorkspace = Workspace::with('workspaceExtras')->find($tmpUser->workspace->id);
            session(['tmp_workspace' => $tmpWorkspace]);
        }

        session(['current_auth' => $user]);

        // User is super adminlayouts/partials/manager/extra_menu.blade.php
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // When is not admin user
        if (!$user->isAdmin() || !in_array($user->platform, [User::PLATFORM_MANAGER, User::PLATFORM_BACKOFFICE])) {
            return redirect('/');
        }

        if(empty($user->active)) {
            return abort(403, json_encode([
                'link' => route($guard.'.login'),
                'user' => $user
            ]));
        }
        
        return $next($request);
    }
}
