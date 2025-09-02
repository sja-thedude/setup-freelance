<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSettingPaymentAPIRequest;
use App\Http\Requests\API\UpdateSettingPaymentAPIRequest;
use App\Models\SettingPayment;
use App\Repositories\SettingPaymentRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class SettingPaymentController
 * @package App\Http\Controllers\API
 */
class SettingPaymentAPIController extends AppBaseController
{
    /** @var  SettingPaymentRepository */
    private $settingPaymentRepository;

    public function __construct(SettingPaymentRepository $settingPaymentRepo)
    {
        parent::__construct();

        $this->settingPaymentRepository = $settingPaymentRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->settingPaymentRepository->pushCriteria(new RequestCriteria($request));
            $this->settingPaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $settingPayments = $this->settingPaymentRepository->paginate($limit);

        return $this->sendResponse($settingPayments->toArray(), trans('setting_payment.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateSettingPaymentAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSettingPaymentAPIRequest $request)
    {
        $input = $request->all();

        $settingPayment = $this->settingPaymentRepository->create($input);

        return $this->sendResponse($settingPayment->toArray(), trans('setting_payment.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SettingPayment $settingPayment */
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            return $this->sendError(trans('setting_payment.not_found'));
        }

        return $this->sendResponse($settingPayment->toArray(), trans('setting_payment.message_read_successfully'));
    }

    /**
     * @param UpdateSettingPaymentAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingPaymentAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var SettingPayment $settingPayment */
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            return $this->sendError(trans('setting_payment.not_found'));
        }

        $settingPayment = $this->settingPaymentRepository->update($input, $id);

        return $this->sendResponse($settingPayment->toArray(), trans('setting_payment.message_update_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var SettingPayment $settingPayment */
        $settingPayment = $this->settingPaymentRepository->findWithoutFail($id);

        if (empty($settingPayment)) {
            return $this->sendError(trans('setting_payment.not_found'));
        }

        $settingPayment->delete();

        return $this->sendResponse($id, trans('setting_payment.message_delete_successfully'));
    }
}
