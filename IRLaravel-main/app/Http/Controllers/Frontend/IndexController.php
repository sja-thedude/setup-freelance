<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mode = $request->get('main_system', false);
        $view = !empty($mode) ? $this->mainHome($request) : $this->restaurantHome($request);       

	 return $view;
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mainHome($request) {
        $isHome = true;
        $headerTitle = trans('dashboard.want_something_tasty');
        return view($this->guard . '.home.index_new', compact('isHome', 'headerTitle'));
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function restaurantHome($request) {
        $host = $request->getHost();
        $workspaceSlug = Helper::getSubDomainOfRequest($host);

        $workspace = session('workspace_'.$workspaceSlug);
        $data = $this->getHomepageData($workspace);

        return view($this->guard . '.home.index', $data);
    }
}
