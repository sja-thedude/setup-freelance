<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateSettingPreferenceRequest;
use App\Http\Requests\UpdateSettingPreferenceRequest;
use App\Repositories\SettingPreferenceRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SettingPreferenceController extends BaseController
{
    /** @var  SettingPreferenceRepository */
    private $settingPreferenceRepository;

    public function __construct(SettingPreferenceRepository $settingPreferenceRepo)
    {
        parent::__construct();

        $this->settingPreferenceRepository = $settingPreferenceRepo;
    }

    /**
     * Display a listing of the SettingPreference.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->settingPreferenceRepository->pushCriteria(new RequestCriteria($request));
        $settingPreferences = $this->settingPreferenceRepository->all();

        return view('admin.setting_preferences.index')
            ->with('settingPreferences', $settingPreferences);
    }

    /**
     * Show the form for creating a new SettingPreference.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.setting_preferences.create');
    }

    /**
     * Store a newly created SettingPreference in storage.
     *
     * @param CreateSettingPreferenceRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingPreferenceRequest $request)
    {
        $input = $request->all();

        $settingPreference = $this->settingPreferenceRepository->create($input);

        Flash::success(trans('setting_preference.message_saved_successfully'));

        return redirect(route('admin.settingPreferences.index'));
    }

    /**
     * Display the specified SettingPreference.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            Flash::error(trans('setting_preference.not_found'));

            return redirect(route('admin.settingPreferences.index'));
        }

        return view('admin.setting_preferences.show')->with('settingPreference', $settingPreference);
    }

    /**
     * Show the form for editing the specified SettingPreference.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            Flash::error(trans('setting_preference.not_found'));

            return redirect(route('admin.settingPreferences.index'));
        }

        return view('admin.setting_preferences.edit')->with('settingPreference', $settingPreference);
    }

    /**
     * Update the specified SettingPreference in storage.
     *
     * @param  int              $id
     * @param UpdateSettingPreferenceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingPreferenceRequest $request)
    {
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            Flash::error(trans('setting_preference.not_found'));

            return redirect(route('admin.settingPreferences.index'));
        }

        $settingPreference = $this->settingPreferenceRepository->update($request->all(), $id);

        Flash::success(trans('setting_preference.message_updated_successfully'));

        return redirect(route('admin.settingPreferences.index'));
    }

    /**
     * Remove the specified SettingPreference from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            Flash::error(trans('setting_preference.not_found'));

            return redirect(route('admin.settingPreferences.index'));
        }

        $this->settingPreferenceRepository->delete($id);

        Flash::success(trans('setting_preference.message_deleted_successfully'));

        return redirect(route('admin.settingPreferences.index'));
    }

    /**
     * @param int $workspaceId
     * @param CreateSettingPreferenceRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreate(int $workspaceId, CreateSettingPreferenceRequest $request)
    {
        $input = $request->all();
        $input['workspace_id'] = $workspaceId;

        $settingPreference = $this->settingPreferenceRepository->updateOrCreatePreference($input);

        return $this->sendResponse($settingPreference->toArray(), trans('setting.preferences.updated_confirm'));
    }
}
