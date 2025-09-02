<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingGeneralAPIRequest;
use App\Http\Requests\API\UpdateSettingGeneralAPIRequest;
use App\Models\SettingGeneral;
use App\Repositories\SettingGeneralRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingGeneralController
 * @package App\Http\Controllers\API
 */
class SettingGeneralAPIController extends AppBaseController
{
    /** @var  SettingGeneralRepository */
    private $settingGeneralRepository;

    public function __construct(SettingGeneralRepository $settingGeneralRepo)
    {
        parent::__construct();

        $this->settingGeneralRepository = $settingGeneralRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingGeneralRepository->pushCriteria(new RequestCriteria($request));
            $this->settingGeneralRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingGenerals = $this->settingGeneralRepository->paginate($limit);

        return $this->sendResponse($settingGenerals->toArray(), trans('setting_general.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingGeneralAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingGeneralAPIRequest $request)
    {
        $input = $request->all();

        $settingGeneral = $this->settingGeneralRepository->create($input);

        return $this->sendResponse($settingGeneral->toArray(), trans('setting_general.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingGeneral $settingGeneral */
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            return $this->sendError(trans('setting_general.not_found'));
        }

        return $this->sendResponse($settingGeneral->toArray(), trans('setting_general.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingGeneralAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingGeneralAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingGeneral $settingGeneral */
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            return $this->sendError(trans('setting_general.not_found'));
        }

        $settingGeneral = $this->settingGeneralRepository->update($input, $id);

        return $this->sendResponse($settingGeneral->toArray(), trans('setting_general.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingGeneral $settingGeneral */
        $settingGeneral = $this->settingGeneralRepository->findWithoutFail($id);

        if (empty($settingGeneral)) {
            return $this->sendError(trans('setting_general.not_found'));
        }

        $settingGeneral->delete();

        return $this->sendResponse($id, trans('setting_general.message_deleted_successfully'));
    }
}
