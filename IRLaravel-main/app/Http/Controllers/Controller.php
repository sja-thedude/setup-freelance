<?php

namespace App\Http\Controllers;

use App\Facades\Helper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Illuminate request class.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Illuminate router class.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Illuminate request class.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * If variable is true then we do not activate sessions
     */
    protected $enableStateless = false;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        //parent::__construct();

        $this->app = app();
        $this->router = $this->app['router'];
        $this->request = $this->app['request'];

        /**
         * Can't call Auth::user() on controller's constructor
         *
         * @link https://laracasts.com/discuss/channels/laravel/cant-call-authuser-on-controllers-constructor
         * @link https://laravel.com/docs/5.3/upgrade#5.3-session-in-constructors
         */
        $this->middleware(function ($request, $next) {
            // Set active workspace
            $workspaceId = $this->request->workspace;

            // If hasn't in URL
            // We will get default workspace
            if (empty($workspaceId)) {
                /** @var \App\Models\Workspace $workspace */
                $workspace = Helper::getDefaultWorkspace();

                if (!empty($workspace)) {
                    $workspaceId = $workspace->id;
                }
            }

            if (!empty($workspaceId)) {
                $workspaceId = (int)$workspaceId;

                // Set active Workspace ID
                Config::set('workspace.active', $workspaceId);
            }

            return $next($request);
        });

        if(empty($this->enableStateless)) {

            // Enabled session variable for KCFINDER
            if (!session_id()) {
                session_start();
            }

            // Overwrite KCFinder config
            $configKCFinder = config('kcfinder');

            if (!empty($configKCFinder)) {
                if (isset($configKCFinder['uploadURL'])) {
                    $configKCFinder['uploadURL'] = url($configKCFinder['uploadURL']);
                }

                // Get from laravel helper
                if (isset($configKCFinder['uploadDir'])) {
                    $configKCFinder['uploadDir'] = public_path($configKCFinder['uploadDir']);
                }

                $_SESSION['KCFINDER'] = $configKCFinder;
            }
        }

    }

    /**
     * @param array $errors
     * @return string|null
     */
    public function getFirstError($errors = null) {
        if ($errors == null || empty($errors) || !is_array($errors) || count($errors) == 0)
            return null;

        /**
         * @var string $field
         * @var array $errors - Array<String>
         */
        foreach ($errors as $field => $messages) {
            return $errors[$field];
        }

        return null;
    }

    /**
     * @param mixed|null $errors
     * @return string|null
     */
    public function getFirstErrorMessage($errors = null) {
        if ($errors !== null) {
            /**
             * @var string $field
             * @var array $errors - Array<String>
             */
            foreach ($errors as $field => $messages) {
                if ($messages !== null && count($messages) > 0)
                    return $messages[0];
            }
        }

        return null;
    }

    /**
     * @param Request $request
     * @param array $inputs
     * @return Request
     */
    public function rejectInputRequest($request, $inputs) {
        foreach ($inputs as $input) {
            if ($request->has($input)) {
                $request->request->remove($input);
            }
        }

        return $request;
    }

    /**
     * Get Homepage data
     *
     * @param \App\Models\Workspace $workspace
     * @return array
     */
    public function getHomepageData(\App\Models\Workspace $workspace)
    {
        $workspaceId = $workspace->id;
        $locale = app()->getLocale();
        $userId = !auth()->guest() ? auth()->user()->id : NULL;

        // get workspace detail
        $workspaceArr = $workspace->getFullInfo();

        // get category list of restaurant
        $categories = $workspace->getListCategories();

        // get setting order type
        $is_takeout = false;
        $is_delivery = false;
        $is_group_order = false;
        $orderTypes = \App\Models\SettingOpenHour::where(['workspace_id' => $workspaceId, 'active' => 1])->get()->toArray();

        foreach ($orderTypes as $orderType) {
            if ($orderType['type'] == 0) {
                $is_takeout = true;
            }

            if ($orderType['type'] == 1) {
                $is_delivery = true;
            }
        }

        foreach ($workspaceArr['extras'] as $extra) {
            if ($extra['type'] == 1) {
                $is_group_order = $extra['active'];
            }
        }

        $general = $workspace->getSettingGeneral();

        return [
            'workspace' => $workspaceArr,
            'is_takeout' => $is_takeout,
            'is_delivery' => $is_delivery,
            'is_group_order' => $is_group_order,
            'categories' => $categories,
            'general' => $general,
            'locale' => $locale,
            'userId' => $userId,
        ];
    }

}
