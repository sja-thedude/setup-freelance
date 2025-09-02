<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Auth;
use App\Traits\APIResponse;

/**
 * Class BaseController
 * @package App\Http\Controllers\Frontend
 */
class BaseController extends Controller
{
    use APIResponse;
    
    /**
     * @var int $perPage
     */
    protected $perPage;
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable $currentUser
     */
    protected $currentUser;
    /**
     * @var string $guard
     */
    protected $guard = 'manager';

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->perPage = config('common.pagination');
        $this->currentUser = null;
        $this->tmpUser = null;
        $this->tmpWorkspace = null;

        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::guard($this->guard)->check() ? Auth::guard($this->guard)->user() : null;
            $this->tmpUser = session('auth_temp');
            
            if (!empty($this->tmpUser)) {
                $this->tmpWorkspace = Workspace::with('workspaceExtras')->find($this->tmpUser->workspace->id);
            }
            
            // If tmpWorkspace does not exist then logout
            if (empty($this->tmpWorkspace)) {
                return redirect(route($this->guard.'.logout'));
            }
            
            return $next($request);
        });
    }
}
