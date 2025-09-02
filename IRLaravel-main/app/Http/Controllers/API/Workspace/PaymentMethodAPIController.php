<?php

namespace App\Http\Controllers\API\Workspace;

use App\Http\Controllers\API\AppBaseController;
use App\Models\SettingPayment;
use App\Repositories\SettingPaymentRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PaymentMethodAPIController
 * @package App\Http\Controllers\API
 */
class PaymentMethodAPIController extends AppBaseController
{
    /**
     * @var SettingPaymentRepository $settingPaymentRepository
     */
    protected $settingPaymentRepository;

    /**
     * SettingPaymentAPIController constructor.
     * @param SettingPaymentRepository $settingPaymentRepo
     */
    public function __construct(SettingPaymentRepository $settingPaymentRepo)
    {
        parent::__construct();

        $this->settingPaymentRepository = $settingPaymentRepo;
    }

    /**
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, int $workspaceId)
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
        $settingPayments = $this->settingPaymentRepository
            ->where('workspace_id', $workspaceId)
            ->paginate($limit);

        $settingPayments->transform(function ($item) {
            /** @var \App\Models\SettingPayment $item */
            return $item->getFullInfo();
        });
        $result = $settingPayments->toArray();

        return $this->sendResponse($result, trans('setting.message_retrieved_list_successfully'));
    }

    /**
     * @param int $workspaceId
     * @param SettingPayment $settingPayment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $workspaceId, SettingPayment $settingPayment)
    {
        $result = $settingPayment->getFullInfo();

        return $this->sendResponse($result, trans('setting.message_retrieved_successfully'));
    }
}
