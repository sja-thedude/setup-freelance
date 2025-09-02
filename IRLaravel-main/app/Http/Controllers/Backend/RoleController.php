<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RoleController
 * @package App\Http\Controllers\Backend
 */
class RoleController extends BaseController
{
    /** @var RoleRepository $roleRepository */
    private $roleRepository;

    /**
     * RoleController constructor.
     * @param RoleRepository $roleRepo
     */
    public function __construct(RoleRepository $roleRepo)
    {
        parent::__construct();

        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the back-office Role.
     *
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $platform = $request->get('platform', Role::PLATFORM_BACKOFFICE);
        $this->roleRepository->scopeQuery(function (\Illuminate\Database\Eloquent\Model $model) use ($platform) {
            return $model->platform($platform);
        });

        $this->roleRepository->pushCriteria(new RequestCriteria($request));
        $roles = $this->roleRepository->paginate($this->perPage);

        return view('admin.roles.index')
            ->with(compact('roles', 'platform'));
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $platform = $request->get('platform', Role::PLATFORM_BACKOFFICE);

        return view('admin.roles.create')
            ->with(compact('platform'));
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRoleRequest $request)
    {
        $input = [
            'active' => Role::IS_YES,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'permission' => $this->_addMorePermissionPosted($request->get('permission')),
        ];

        $role = $this->roleRepository->create($input);

        Flash::success(trans('messages.role.created_successfully'));

        return redirect(route('admin.roles.index', ['platform' => $role->platform]));
    }

    /**
     * Display the specified Role.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $role = $this->roleRepository->findWithoutFail($id);

        if (empty($role)) {
            Flash::error(trans('messages.role.not_found'));

            return redirect(route('admin.roles.index'));
        }

        return view('admin.roles.edit')->with('role', $role);
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->findWithoutFail($id);

        if (empty($role)) {
            Flash::error(trans('messages.role.not_found'));

            return redirect(route('admin.roles.index'));
        }

        return view('admin.roles.edit')->with('role', $role);
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  int $id
     * @param UpdateRoleRequest $request
     *
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRoleRequest $request)
    {
        /** @var \App\Models\Role $role */
        $role = $this->roleRepository->findWithoutFail($id);

        if (empty($role)) {
            Flash::error(trans('messages.role.not_found'));

            return redirect(route('admin.roles.index'));
        }

        $input = [
            'active' => $request->get('active', $role->active),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'permission' => $this->_addMorePermissionPosted($request->get('permission')),
        ];

        $role = $this->roleRepository->update($input, $id);

        Flash::success(trans('messages.role.updated_successfully'));

        return redirect()->back();
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $role = $this->roleRepository->findWithoutFail($id);

        if (empty($role)) {
            Flash::error(trans('messages.role.not_found'));

            return redirect(route('admin.roles.index'));
        }

        $this->roleRepository->delete($id);

        Flash::success(trans('messages.role.deleted_successfully'));

        return redirect()->back();
    }

    /**
     * Add more permission to permission posted (Example : if permission is create, need to add store)
     *
     * @param array $permission
     * @return array
     */
    protected function _addMorePermissionPosted($permission)
    {
        // Check duplicate and get unique values
        $permission = $this->_checkDuplicatePermissions($permission);
        $permission['dashboard'][] = 'default@index';
        $permission['login'][] = 'login@logout';
        $permission['auth'][] = 'auth@logout';

        if (!empty($permission)) {
            foreach ($permission as $key => $arrAction) {
                foreach ($arrAction as $action) {
                    $fieldData = explode('@', $action);

                    switch ($fieldData[1]) {
                        case 'create':
                            if (!in_array($fieldData[0] . '@store', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@store';
                            }

                            if (!in_array($fieldData[0] . '@index', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@index';
                            }

                            if (!in_array($fieldData[0] . '@show', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@show';
                            }
                            break;
                        case 'edit':
                            if (!in_array($fieldData[0] . '@update', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@update';
                            }

                            if (!in_array($fieldData[0] . '@index', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@index';
                            }

                            if (!in_array($fieldData[0] . '@show', $permission[$key])) {
                                $permission[$key][] = $fieldData[0] . '@show';
                            }
                            break;
                    }
                }
            }
        }

        return $permission;
    }

    /**
     * Check duplicate and get unique values
     *
     * @param array $allPermission
     * @return array
     */
    protected function _checkDuplicatePermissions($allPermission) {
        if ($allPermission && is_array($allPermission)) {
            foreach ($allPermission as $controller => $actions) {
                if (is_array($actions)) {
                    $allPermission[$controller] = array_values(array_unique($actions));
                }
            }
        }

        return $allPermission;
    }

}
