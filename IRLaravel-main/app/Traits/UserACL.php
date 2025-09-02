<?php

namespace App\Traits;

trait UserACL {

    /**
     * Make string to array if already not
     *
     * @param  Mixed $perm String/Array
     * @return array
     */
    protected function getArray($perm)
    {
        return is_array($perm) ? $perm : explode('|', $perm);
    }

    /**
     * Check if the permission matches with any permission user has
     *
     * @param  array $permArray Name of a permission (one or more separated with |)
     * @return Boolean true if permission exists, otherwise false
     */
    protected function checkPermission(array $permArray = [])
    {
        $perms = [];
        $accessRoute = [];
        //Covert access route to permission access        
        foreach($permArray as $key=>$route){
            $route = strtolower(str_replace('Controller','',$route));
            $accessRoute[$key] = $route;           
        }  

        //Check role & permission access 
        if (!empty($this->roles)) {
            $permGroups = (empty($this->roles->permission)) ? array() : $this->roles->permission;
            if (!empty($permGroups)) {
                foreach ($permGroups as $key => $item) {                    
                    $perms = array_merge($perms, array_reduce($item, function($carry, $action) use ($key) {
                        $carry[] = $action;
                       
                        return $carry;
                    }, []));                    
                }
            }
        }
        
        return count(array_intersect($perms, $accessRoute));
    }

    /**
     * Checks if has a Permission
     *
     * @param  string $permission [Name of a permission]
     * @return Boolean true if has permission, otherwise false
     */
    public function hasPermission($permission = null)
    {                      
        if($permission) {
            return $this->checkPermission($this->getArray($permission));
        }

        return false;
    }

    /**
     * Checks if has a role
     *
     * @param  String $role [Name of a Role: Slug field in DB]
     * @return Boolean true if has permission, otherwise false
     */
    public function hasRole($role = null)
    {
        if(is_null($role)) return false;

        return strtolower($this->role->role_slug) == strtolower($role);
    }

    /**
     * Check if user has given role
     *
     * @param  String $role role_slug
     * @return Boolean TRUE or FALSE
     */
    /*public function is($role)
    {
        return $this->role == $role;
    }*/

    /**
     * Check if user has permission to a route
     *
     * @param  String $routeName
     * @return Boolean true/false
     */
    public function hasRoute($routeName)
    {
        $route = app('router')->getRoutes()->getByName($routeName);

        if($route) {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                $controller = isset($action['namespace']) ? explode("{$action['namespace']}\\", $action['controller']) : [];

                $required = !empty($controller) ? (array)$controller[1] : (array)$action['controller'];

                return $this->checkPermission($required);
            }
        }

        return false;
    }

    /**
     * Check if a top level menu is visible to user
     *
     * @param  String $perm
     * @return Boolean true/false
     */
    public function canSeeMenuItem($perm)
    {
        return $this->hasPermission($perm) || $this->hasAnylike($perm);
    }

    /**
     * Checks if user has any permission in this group
     *
     * @param  String $perm Required Permission
     * @return Boolean true/false
     */
    protected function hasAnylike($perm)
    {
        $parts = explode('_', $perm);

        $requiredPerm = array_pop($parts);

        $perms = $this->role->permissions->fetch('permission_slug');

        foreach ($perms as $perm)
        {
            if(ends_with($perm, $requiredPerm)) return true;
        }

        return false;
    }

    public function hasRouteUseConfigPermissions($routeName, $guardDetail)
    {
        $route = app('router')->getRoutes()->getByName($routeName);

        if($route) {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                $controller = isset($action['namespace']) ? explode("{$action['namespace']}\\", $action['controller']) : [];
                $required = !empty($controller) ? $controller[1] : $action['controller'];
                $routeAction = strtolower(str_replace('Controller', '', $required));
                $roleCacheName = config('cache.key'). $guardDetail;
                $rolePermission = $this->getRolePermission($roleCacheName, $guardDetail);

                return in_array($routeAction, $rolePermission);
            }
        }

        return false;
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
     * Add more permission to permission posted (Example : if permission is create, need to add store)
     *
     * @param array $permission
     * @return array
     */
    protected function _addMorePermissionPosted($permission)
    {
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
