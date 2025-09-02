<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfSettingAccessNotification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $tmpUser = session('auth_temp');
        $workspace = Workspace::with('workspaceExtras')->find($tmpUser->workspace_id);

        if (!$tmpUser || !$workspace) {
            return abort(404);
        }

        $isShowNotification = $workspace
            ->workspaceExtras->where('type', \App\Models\WorkspaceExtra::OWN_MOBILE_APP)
            ->first();

        if ($isShowNotification && $isShowNotification->active) {
            return $next($request);
        }

        return abort(404);
    }
}
