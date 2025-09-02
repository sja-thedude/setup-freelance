<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
    protected $guard = 'web';

    protected $workspaceSlug;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->perPage = config('common.pagination');

        $this->middleware(function ($request, $next) {
            $host = $request->getHost();
            $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
            $this->workspaceSlug = $workspaceSlug;

            return $next($request);
        });
    }
}
