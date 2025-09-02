<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use \App\Traits\APIResponse;

/**
 * Class BackendController
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
    protected $guard = 'admin';

    /**
     * BackendController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->perPage = config('common.pagination');
        $this->currentUser = null;

        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::guard($this->guard)->check() ? Auth::guard($this->guard)->user() : null;

            return $next($request);
        });
    }
}
