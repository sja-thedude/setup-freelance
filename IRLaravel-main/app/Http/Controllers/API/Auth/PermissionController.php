<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Facades\Helper;

class PermissionController extends AppBaseController
{

    public function getPermissions(Request $request)
    {
        // $guard = 'admin'; // For testing
        $guard = null;
        $baseUrl = URL::to('/') . '/';
        $adminPrefix = 'admin.';
        $apiPrefix = 'api.';
        $data = [];
        $allowPermissions = [];
        // Restrict permissions
        $tmpRestrictPermissions = [
            'profile.show',
            'profile.update',
            'countries.destroy',
        ];
        $restrictPermissions = [];
        $routes = Route::getRoutes();
        $routeNames = $routes->getRoutesByName();

        if (!empty($routeNames)) {
            // Virtual ID
            $id = 1;

            foreach ($routeNames as $name => $route) {
                // All permissions - Get all from API with prefix is api.

                // Allow permissions - Check by web admin
                if (Str::startsWith($name, $adminPrefix)) {
                    // Get short name of api (name)
                    // Wee need to naming both admin and api are the same
                    // Only difference the prefixes (admin. and api.)
                    $shortName = Str::replaceFirst($adminPrefix, '', $name);
                    $apiRouteName = $apiPrefix . $shortName;

                    // Check API route
                    if (Route::has($apiRouteName)) {
                        $apiRoute = $routes->getByName($apiRouteName);
                        $prefix = ltrim($apiRoute->action['prefix'], '/') . '/';
                        $item = [
                            'id' => $id,
                            'name' => $shortName,
                            'prefix' => $prefix,
                            'uri' => $apiRoute->uri,
                            'short_uri' => Str::replaceFirst($prefix, '', $apiRoute->uri),
                            'full_path' => $baseUrl . $apiRoute->uri,
                            'methods' => Arr::first($apiRoute->methods, null, 'GET'),
                        ];

                        // Push to all_permissions
                        $data[$shortName] = $item;

                        // Check allow_permissions
                        if (Auth::guard($guard)->user() && Helper::checkUserPermission($name, $guard)) {
                            $allowPermissions[] = $shortName;
                        }

                        // Push to restrict_permissions
                        if (in_array($shortName, $tmpRestrictPermissions)) {
                            $restrictPermissions[] = $shortName;
                        }
                    }
                }

                $id++;
            }
        }

        $result = [
            'all_permissions' => $data,
            'allow_permissions' => array_merge($allowPermissions, $restrictPermissions),
            'restrict_permissions' => $restrictPermissions,
        ];

        return $this->sendResponse($result, 'Permissions are retrieved successfully');
    }

}
