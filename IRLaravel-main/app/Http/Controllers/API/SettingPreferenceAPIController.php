<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingPreferenceAPIRequest;
use App\Http\Requests\API\UpdateSettingPreferenceAPIRequest;
use App\Models\SettingPreference;
use App\Repositories\SettingPreferenceRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingPreferenceController
 * @package App\Http\Controllers\API
 */
class SettingPreferenceAPIController extends AppBaseController
{
    /** @var  SettingPreferenceRepository */
    private $settingPreferenceRepository;

    public function __construct(SettingPreferenceRepository $settingPreferenceRepo)
    {
        parent::__construct();

        $this->settingPreferenceRepository = $settingPreferenceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingPreferenceRepository->pushCriteria(new RequestCriteria($request));
            $this->settingPreferenceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingPreferences = $this->settingPreferenceRepository->paginate($limit);

        return $this->sendResponse($settingPreferences->toArray(), trans('setting_preference.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingPreferenceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingPreferenceAPIRequest $request)
    {
        $input = $request->all();

        $settingPreference = $this->settingPreferenceRepository->create($input);

        return $this->sendResponse($settingPreference->toArray(), trans('setting_preference.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingPreference $settingPreference */
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            return $this->sendError(trans('setting_preference.not_found'));
        }

        return $this->sendResponse($settingPreference->toArray(), trans('setting_preference.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingPreferenceAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingPreferenceAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingPreference $settingPreference */
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            return $this->sendError(trans('setting_preference.not_found'));
        }

        $settingPreference = $this->settingPreferenceRepository->update($input, $id);

        return $this->sendResponse($settingPreference->toArray(), trans('setting_preference.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingPreference $settingPreference */
        $settingPreference = $this->settingPreferenceRepository->findWithoutFail($id);

        if (empty($settingPreference)) {
            return $this->sendError(trans('setting_preference.not_found'));
        }

        $settingPreference->delete();

        return $this->sendResponse($id, trans('setting_preference.message_deleted_successfully'));
    }
}
