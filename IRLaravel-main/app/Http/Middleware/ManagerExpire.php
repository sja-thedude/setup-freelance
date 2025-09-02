<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;

class ManagerExpire
{
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
        if ($user = \Auth::guard('admin')->user() || Helper::checkExpires($guard)) {
            return $next($request);
        }

        return abort(403, json_encode([
            'link' => route($guard . '.login'),
            'user' => $user
        ]));
    }
}
