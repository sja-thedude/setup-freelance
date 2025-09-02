<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\UpdateWorkspaceManagerRequest;
use App\Models\Media;
use App\Models\Option;
use App\Models\RestaurantCategory;
use App\Models\SettingPrint;
use App\Models\Workspace;
use App\Models\WorkspaceCategory;
use App\Models\WorkspaceExtra;
use App\Repositories\MediaRepository;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkspaceRepository;
use App\Repositories\SettingGeneralRepository;
use App\Repositories\SettingPaymentRepository;
use App\Repositories\SettingPreferenceRepository;
use App\Repositories\SettingDeliveryConditionsRepository;
use App\Repositories\SettingPrintRepository;
use App\Repositories\SettingOpenHourRepository;
use App\Repositories\SettingExceptHourRepository;
use App\Repositories\SettingTimeslotRepository;
use Illuminate\Http\Request;
use Flash;
use DB;
use Log;

/**
 * Class SettingController
 * @package App\Http\Controllers\Manager
 */
class SettingController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    private $workspaceRepository;
    private $settingGeneralRepository;
    private $settingPaymentRepository;
    private $settingConnectorRepository;
    private $settingPreferenceRepository;
    private $settingDeliveryConditionsRepository;
    private $settingPrintRepository;
    private $settingOpenHourRepository;
    private $settingExceptHourRepository;
    private $settingTimeslotRepository;
    private $mediaRepository;

    /**
     * SettingController constructor.
     * @param UserRepository $userRepo
     * @param WorkspaceRepository $workspaceRepo
     * @param SettingGeneralRepository $settingGeneralRepo
     * @param SettingPaymentRepository $settingPaymentRepo
     * @param SettingPreferenceRepository $settingPreferenceRepo
     * @param SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo
     * @param SettingPrintRepository $settingPrintRepo
     * @param MediaRepository $mediaRepository
     */
    public function __construct(
        UserRepository $userRepo,
        WorkspaceRepository $workspaceRepo,
        SettingGeneralRepository $settingGeneralRepo,
        SettingPaymentRepository $settingPaymentRepo,
        SettingConnectorRepository $settingConnectorRepo,
        SettingPreferenceRepository $settingPreferenceRepo,
        SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo,
        SettingPrintRepository $settingPrintRepo,
        SettingOpenHourRepository $settingOpenHourRepo,
        SettingExceptHourRepository $settingExceptHourRepo,
        SettingTimeslotRepository $settingTimeslotRepo,
        MediaRepository $mediaRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepo;
        $this->workspaceRepository = $workspaceRepo;
        $this->settingGeneralRepository = $settingGeneralRepo;
        $this->settingPaymentRepository = $settingPaymentRepo;
        $this->settingConnectorRepository = $settingConnectorRepo;
        $this->settingPreferenceRepository = $settingPreferenceRepo;
        $this->settingDeliveryConditionsRepository = $settingDeliveryConditionsRepo;
        $this->settingPrintRepository = $settingPrintRepo;
        $this->settingOpenHourRepository = $settingOpenHourRepo;
        $this->settingExceptHourRepository = $settingExceptHourRepo;
        $this->settingTimeslotRepository = $settingTimeslotRepo;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function general(Request $request)
    {
        $types = RestaurantCategory::all()->pluck('name', 'id');
        $languages = Helper::getActiveLanguages();
        $settingGeneral = $this->settingGeneralRepository->findWhere(['workspace_id' => $this->tmpWorkspace->id])->first();

        return view($this->guard.'.settings.general')->with(compact(
            'types',
            'languages',
            'settingGeneral'
        ));
    }

    /**
     * @todo move to SettingPaymentController
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentMethods(Request $request) {
        $workspaceId = $this->tmpWorkspace->id;
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        $settingPayments = $this->settingPaymentRepository->findWhere(['workspace_id' => $workspaceId]);
        $payconiq = WorkspaceExtra::getOneExtraByType($workspaceId, WorkspaceExtra::PAYCONIQ);
        $enableInHouse = $workspace->enableInHouse();
        $enableSelfOrdering = $workspace->enableSelfOrdering();
        $connectorsList = $this->getConnectorsList();

        $settingPaymentReferences = null;
        if(!empty($connectorsList)) {
            $settingPaymentReferences = $this->settingPaymentRepository->getSettingPaymentReferencesByWorkspace($workspaceId);
        }

        return view($this->guard.'.settings.payment_methods')->with(compact(
            'settingPayments',
            'payconiq',
            'connectorsList',
            'settingPaymentReferences',
            'enableInHouse',
            'enableSelfOrdering'
        ));
    }

    /**
     * @return mixed|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preferences(Request $request) {
        $workspaceId = $this->tmpWorkspace->id;
        $settingPreferences = $this->settingPreferenceRepository->findWhere(['workspace_id' => $workspaceId])->first();
        $options = Option::getOptionsList($workspaceId);
        $serviceCost = WorkspaceExtra::getOneExtraByType($workspaceId, WorkspaceExtra::SERVICE_COST);

        return view($this->guard.'.settings.preferences')->with(compact(
            'settingPreferences',
            'options',
            'serviceCost'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deliveryConditions(Request $request) {
        $settingDeliveryConditions = $this->settingDeliveryConditionsRepository->findWhere(['workspace_id' => $this->tmpWorkspace->id]);

        return view($this->guard.'.settings.delivery_conditions')->with(compact(
            'settingDeliveryConditions'
        ));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function print(Request $request) {
        $receiptPrinter = $this->settingPrintRepository->getSettingPrintByType($this->tmpWorkspace->id, SettingPrint::TYPE_KASSABON);
        $workOrderPrinter = $this->settingPrintRepository->getSettingPrintByType($this->tmpWorkspace->id, SettingPrint::TYPE_WERKBON);
        $stickerPrinter = $this->settingPrintRepository->getSettingPrintByType($this->tmpWorkspace->id, SettingPrint::TYPE_STICKER)->first();
        $printTypes = SettingPrint::getTypes();

        return view($this->guard.'.settings.print')->with(compact(
            'receiptPrinter',
            'workOrderPrinter',
            'stickerPrinter',
            'printTypes'
        ));
    }

    /**
     * @param UpdateWorkspaceManagerRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function updateWorkspace(UpdateWorkspaceManagerRequest $request, $id)
    {
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            Flash::error(trans('workspace.not_found'));

            return redirect(route('admin.workspaces.index'));
        }

        $input = $request->all();
        //Upload logo
        if (!empty($request->uploadAvatar)) {
            $input['files']['file'][] = $request->uploadAvatar;
        }

        //Upload gallery
        if (!empty($request->galleries)) {
            $input['galleries']['file'] = $request->galleries;
        }

        $input = array_merge($input, [
            'facebook_enabled' => $workspace->facebook_enabled,
            'google_enabled' => $workspace->google_enabled,
            'apple_enabled' => $workspace->apple_enabled,
        ]);

        DB::beginTransaction();
        try {
            $workspace = $this->workspaceRepository->updateWorkspace($input, $id);

            $this->userRepository->syncWorkspaceInfoToUser($request, $workspace->user, $workspace);

            //sync categories
            if((isset($input['types']) && !empty($input['types'])) || !empty($workspace->workspaceCategories)) {
                WorkspaceCategory::syncCategories($workspace, $input['types']);
            }

            DB::commit();

            if($request->ajax()) {
                return $this->sendResponse($workspace, trans('workspace.updated_confirm'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(__FILE__ . ' - ' . $e->getMessage());

            return $this->sendError(null, 400, $e->getMessage());
        }

        return redirect(route('admin.workspaces.index'));
    }

    public function openingHours(Request $request) {
        $workspaceId = $this->tmpWorkspace->id;
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        $settings = $this->settingOpenHourRepository->getOpenHourByWorkspace($workspaceId);
        $settingHolidays = $this->settingExceptHourRepository->getSettingByWorkspace($workspaceId);
        $dayInWeek = config('common.day_in_week');
        $enableInHouse = $workspace->enableInHouse();
        $enableSelfOrdering = $workspace->enableSelfOrdering();
        $connectorsList = $this->getConnectorsList();

        $settingOpenHourReferences = null;
        if(!empty($connectorsList)) {
            $settingOpenHourReferences = $this->settingOpenHourRepository->getSettingOpenhourReferencesByWorkspace($this->tmpWorkspace->id);
        }

        return view($this->guard.'.settings.open_hours')
            ->with(compact(
                'settings',
                'settingHolidays',
                'connectorsList',
                'settingOpenHourReferences',
                'workspaceId',
                'dayInWeek',
                'enableInHouse',
                'enableSelfOrdering'
            ));
    }

    public function timeSlots(Request $request) {
        $workspaceId = $this->tmpWorkspace->id;
        $settings = $this->settingTimeslotRepository->getTimeSlotByWorkspace($workspaceId);
        $settingOpenHourActive = [];
        $settingOpenHours = $this->settingOpenHourRepository
            ->makeModel()
            ->where('workspace_id', $workspaceId)
            ->where('active', true)
            ->get();

        if(!$settingOpenHours->isEmpty()) {
            $settingOpenHourActive = $settingOpenHours->pluck('type')->toArray();
        }

        $dayInWeek = config('common.day_in_week');

        return view($this->guard.'.settings.time_slots')
            ->with(compact(
                'workspaceId',
                'settings',
                'settingOpenHourActive',
                'dayInWeek'
            ));
    }

    public function uploadGallery(Request $request, $id)
    {
        try {
            $input = [];
            $galleryType = $request->galleryType;
            $input[$galleryType]['file'] = $request->file;
            $collection = $this->workspaceRepository->uploadGalleries($input, $id, $galleryType);
            if (!empty($collection)) {
                $file = $collection->toArray();
                $response = [
                    'file' => $file
                ];

                return $this->sendResponse($response, trans('messages.upload_successfully'));
            }

            return $this->sendError(trans('messages.upload_fail'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update/delete gallery
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGalleryOrder(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $orderedList = $request->get('order');
            $galleryType = $request->get('galleryType');
            if ($orderedList) {
                foreach ($orderedList as $order => $mediaId) {
                    $this->mediaRepository->update(['order' => $order + 1], $mediaId);
                }
            }

            if ($request->get('mediaId')) {
                $isDelete = $request->get('is_delete');
                if ($isDelete) {
                    $this->mediaRepository->delete($request->get('mediaId'));
                } else {
                    $status = $request->get('status');
                    $this->mediaRepository->update(['active' => $status], $request->get('mediaId'));
                }
            }

            $workspace = $this->workspaceRepository->find($id);
            $galleryCollection = $workspace->workspaceGalleries;
            if ($galleryType == Media::API_GALLERIES) {
                $galleryCollection = $workspace->workspaceAPIGalleries;
            }

            $previewGallery = $galleryCollection->filter(function ($value, $key) {
                return ($value->active == 1);
            })->sortBy('order')->first();

            \DB::commit();

            return $this->sendResponse(['previewGallery' => !empty($previewGallery)?$previewGallery->full_path:0], trans('messages.success'));
        } catch (\Exception $e) {
            \DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }
}
