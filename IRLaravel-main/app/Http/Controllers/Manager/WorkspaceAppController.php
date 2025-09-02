<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\ChangeAppSettingsRequest;
use App\Http\Requests\UpdateWorkspaceAppRequest;
use App\Models\WorkspaceAppMeta;
use App\Repositories\WorkspaceAppMetaRepository;
use App\Repositories\WorkspaceAppRepository;
use Illuminate\Http\Request;
use Flash;
use Response;

class WorkspaceAppController extends BaseController
{
    /**
     * @var WorkspaceAppRepository $workspaceAppRepository
     */
    protected $workspaceAppRepository;

    /**
     * @var WorkspaceAppMetaRepository $workspaceAppMetaRepository
     */
    protected $workspaceAppMetaRepository;

    /**
     * WorkspaceAppController constructor.
     * @param WorkspaceAppRepository $workspaceAppRepo
     * @param WorkspaceAppMetaRepository $workspaceAppMetaRepo
     */
    public function __construct(WorkspaceAppRepository $workspaceAppRepo, WorkspaceAppMetaRepository $workspaceAppMetaRepo)
    {
        parent::__construct();

        $this->workspaceAppRepository = $workspaceAppRepo;
        $this->workspaceAppMetaRepository = $workspaceAppMetaRepo;
    }

    /**
     * Display a listing of the WorkspaceApp.
     *
     * @param Request $request
     * @return Response
     */
    public function theme(Request $request)
    {
        /** @var \App\Models\Workspace $workspace */
        $workspace = $this->tmpWorkspace;
        $workspaceId = $workspace->id;

        $workspaceApp = $workspace->workspaceApp;

        if (empty($workspaceApp)) {
            $workspaceApp = $this->workspaceAppRepository->create([
                'workspace_id' => $workspaceId,
            ]);
        }

        return view('manager.workspace_apps.theme')
            ->with([
                'workspaceApp' => $workspaceApp,
                'workspaceId' => $workspaceId,
            ]);
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param int $id Theme ID. get from config/app_theme.php
     * @param UpdateWorkspaceAppRequest $request
     *
     * @return Response
     */
    public function changeTheme($id, Request $request)
    {
        /** @var \App\Models\Workspace $workspace */
        $workspace = $this->tmpWorkspace;
        $workspaceId = $workspace->id;

        $workspaceApp = $this->workspaceAppRepository->changeTheme($workspaceId, $id);

        if (!empty($workspaceApp)) {
            return $this->sendResponse(null, 'You has changed theme successfully.');
        }

        Flash::success('Workspace App is updated successfully.');

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Display a listing of the WorkspaceApp.
     *
     * @param Request $request
     * @return Response
     */
    public function settings(Request $request)
    {
        /** @var \App\Models\Workspace $workspace */
        $workspace = $this->tmpWorkspace;
        $workspaceId = $workspace->id;

        $workspaceApp = $workspace->workspaceApp;

        if (empty($workspaceApp)) {
            $workspaceApp = $this->workspaceAppRepository->create([
                'workspace_id' => $workspaceId,
            ]);
        }

        return view('manager.workspace_apps.settings')
            ->with([
                'workspaceApp' => $workspaceApp,
                'workspaceId' => $workspaceId,
            ]);
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function storeSetting(ChangeAppSettingsRequest $request)
    {
        $input = $request->all();

        $workspaceAppMeta = $this->workspaceAppMetaRepository->create($input);

        $result = $workspaceAppMeta->toArray();

        // New URLs
        $result = array_merge($result, [
            'url_update' => route('manager.apps.change_settings', ['id' => $workspaceAppMeta->id]),
            'url_change_status' => route('manager.apps.settings.change_status', ['id' => $workspaceAppMeta->id]),
            'url_destroy' => route('manager.apps.settings.destroy', ['id' => $workspaceAppMeta->id]),
        ]);

        if ($request->ajax()) {
            return $this->sendResponse($result, trans('workspace_app.message_created_setting_successfully'));
        }

        Flash::success(trans('workspace_app.message_created_setting_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function changeSettings($id, ChangeAppSettingsRequest $request)
    {
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        $input = $request->all();

        $workspaceAppMeta = $this->workspaceAppMetaRepository->update($input, $id);

        $result = $workspaceAppMeta->toArray();

        // New URLs
        $result = array_merge($result, [
            'url_update' => route('manager.apps.change_settings', ['id' => $workspaceAppMeta->id]),
            'url_change_status' => route('manager.apps.settings.change_status', ['id' => $workspaceAppMeta->id]),
            'url_destroy' => route('manager.apps.settings.destroy', ['id' => $workspaceAppMeta->id]),
        ]);

        if ($request->ajax()) {
            return $this->sendResponse($result, trans('workspace_app.message_updated_setting_successfully'));
        }

        Flash::success(trans('workspace_app.message_updated_setting_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function changeSettingStatus($id, Request $request)
    {
        /** @var \App\Models\WorkspaceAppMeta $workspaceAppMeta */
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            Flash::error(trans('workspace_app.not_found'));

            return redirect(route('admin.workspaceApps.index'));
        }

        $input = $request->all();
        // Default is toggle
        $status = !$workspaceAppMeta->active;

        if (array_key_exists('status', $input)) {
            // Get status from client request
            $status = filter_var($request->get('status'), FILTER_VALIDATE_BOOLEAN);
        }

        $workspaceAppMeta = $this->workspaceAppMetaRepository->changeStatus($workspaceAppMeta, $status);

        if ($request->ajax()) {
            return $this->sendResponse(null, trans('workspace_app.message_updated_status_successfully'));
        }

        Flash::success(trans('workspace_app.message_updated_status_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Throwable
     */
    public function createSetting(Request $request)
    {
        /** @var \App\Models\Workspace $workspace */
        $workspace = $this->tmpWorkspace;
        $workspaceId = $workspace->id;

        $workspaceApp = $workspace->workspaceApp;

        if (empty($workspaceApp)) {
            throw new \Exception('Not found config app for workspace #' . $workspaceId);
        }

        $workspaceAppId = $workspaceApp->id;

        /** @var \App\Models\WorkspaceAppMeta $lastItem */
        $lastItem = \App\Models\WorkspaceAppMeta::where('workspace_app_id', $workspaceAppId)
            ->orderBy('order', 'DESC')
            ->select(['id', 'order'])
            ->first();

        $lastOrder = 1;

        if (!empty($lastItem)) {
            $lastOrder = $lastItem->order + 1;
        }

        $appMeta = new \App\Models\WorkspaceAppMeta([
            'active' => true,
            'order' => $lastOrder,
            'workspace_app_id' => $workspaceAppId,
            'default' => false,
            'name' => 'Item ' . $lastOrder,
            'type' => \App\Models\WorkspaceAppMeta::TYPE_1,
        ]);

        if ($request->ajax()) {
            $html = view('manager.workspace_apps.partials.setting_item', [
                'appMeta' => $appMeta,
            ])->render();

            return $this->sendResponse($html, trans('workspace_app.message_created_setting_successfully'));
        }

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroySetting($id, Request $request)
    {
        /** @var WorkspaceAppMeta $workspaceAppMeta */
        $workspaceAppMeta = $this->workspaceAppMetaRepository->findWithoutFail($id);

        if (empty($workspaceAppMeta)) {
            return $this->sendError(trans('workspace_app.not_found'));
        }

        $workspaceAppMeta->delete();

        if ($request->ajax()) {
            return $this->sendResponse($id, trans('workspace_app.message_deleted_setting_successfully'));
        }

        Flash::success(trans('workspace_app.message_deleted_setting_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

    /**
     * Update the specified WorkspaceApp in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function changeSettingOrders(Request $request)
    {
        $orders = $request->get('orders');

        $workspaceAppMeta = $this->workspaceAppMetaRepository->changeOrders($orders);

        if ($request->ajax()) {
            return $this->sendResponse(null, trans('workspace_app.message_updated_setting_successfully'));
        }

        Flash::success(trans('workspace_app.message_updated_setting_successfully'));

        return redirect(route('admin.workspaceApps.index'));
    }

}
