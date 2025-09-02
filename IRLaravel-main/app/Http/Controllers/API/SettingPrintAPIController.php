<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingPrintAPIRequest;
use App\Http\Requests\API\UpdateSettingPrintAPIRequest;
use App\Models\SettingPrint;
use App\Repositories\SettingPrintRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingPrintController
 * @package App\Http\Controllers\API
 */
class SettingPrintAPIController extends AppBaseController
{
    /** @var  SettingPrintRepository */
    private $settingPrintRepository;

    public function __construct(SettingPrintRepository $settingPrintRepo)
    {
        parent::__construct();

        $this->settingPrintRepository = $settingPrintRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingPrintRepository->pushCriteria(new RequestCriteria($request));
            $this->settingPrintRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingPrints = $this->settingPrintRepository->paginate($limit);

        return $this->sendResponse($settingPrints->toArray(), trans('setting_print.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingPrintAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingPrintAPIRequest $request)
    {
        $input = $request->all();

        $settingPrint = $this->settingPrintRepository->create($input);

        return $this->sendResponse($settingPrint->toArray(), trans('setting_print.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingPrint $settingPrint */
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            return $this->sendError(trans('setting_print.not_found'));
        }

        return $this->sendResponse($settingPrint->toArray(), trans('setting_print.message_retrieved_successfully'));
    }

    /**
     * @param UpdateSettingPrintAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingPrintAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingPrint $settingPrint */
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            return $this->sendError(trans('setting_print.not_found'));
        }

        $settingPrint = $this->settingPrintRepository->update($input, $id);

        return $this->sendResponse($settingPrint->toArray(), trans('setting_print.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingPrint $settingPrint */
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            return $this->sendError(trans('setting_print.not_found'));
        }

        $settingPrint->delete();

        return $this->sendResponse($id, trans('setting_print.message_deleted_successfully'));
    }
}
