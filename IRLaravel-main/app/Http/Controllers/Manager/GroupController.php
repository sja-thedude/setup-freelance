<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use App\Models\PrinterJob;
use App\Models\ProductTranslation;
use App\Models\RedeemHistory;
use App\Models\Vat;
use App\Repositories\CategoryOptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\GroupProductRepository;
use App\Repositories\GroupRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionItemRepository;
use App\Repositories\OptionRepository;
use App\Repositories\PrinterJobRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductLabelRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StatisticRepository;
use App\Repositories\VatRepository;
use App\Repositories\WorkspaceRepository;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\CategoryRelationRepository;
use Log;

/**
 * Class GroupController
 *
 * @package App\Http\Controllers\Manager
 */
class GroupController extends BaseController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    
    /**
     * @var CategoryOptionRepository
     */
    private $categoryOptionRepository;
    
    /**
     * @var ProductOptionRepository
     */
    private $productOptionRepository;
    
    /**
     * @var OpenTimeslotRepository
     */
    private $openTimeslotRepository;
    
    /**
     * @var MediaRepository
     */
    private $mediaRepository;
    
    /**
     * @var ProductRepository
     */
    private $productRepository;
    
    /**
     * @var VatRepository
     */
    private $vatRepository;
    
    /**
     * @var ProductAllergenenRepository
     */
    private $productAllergenenRepository;
    
    /**
     * @var ProductLabelRepository
     */
    private $productLabelRepository;
    
    /**
     * @var OptionItemRepository
     */
    private $optionItemRepository;
    
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    
    /**
     * @var GroupProductRepository
     */
    private $groupProductRepository;
    
    private $workspaceRepository;
    private $statisticRepository;
    private $printerJobRepository;
    private $categoryRelationRepository;
    
    /**
     * GroupController constructor.
     * @param  CategoryRepository  $categoryRepository
     * @param  OptionRepository  $optionRepository
     * @param  CategoryOptionRepository  $categoryOptionRepository
     * @param  ProductOptionRepository  $productOptionRepository
     * @param  ProductAllergenenRepository  $productAllergenenRepository
     * @param  ProductLabelRepository  $productLabelRepository
     * @param  OpenTimeslotRepository  $openTimeslotRepository
     * @param  MediaRepository  $mediaRepository
     * @param  ProductRepository  $productRepository
     * @param  VatRepository  $vatRepository
     * @param  OptionItemRepository  $optionItemRepository
     * @param  GroupRepository  $groupRepository
     * @param  GroupProductRepository  $groupProductRepository
     * @param  WorkspaceRepository  $workspaceRepo
     * @param  StatisticRepository  $statisticRepo
     * @param  PrinterJobRepository  $printerJobRepo
     * @param  CategoryRelationRepository  $categoryRelationRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        CategoryOptionRepository $categoryOptionRepository,
        ProductOptionRepository $productOptionRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        ProductLabelRepository $productLabelRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        VatRepository $vatRepository,
        OptionItemRepository $optionItemRepository,
        GroupRepository $groupRepository,
        GroupProductRepository $groupProductRepository,
        WorkspaceRepository $workspaceRepo,
        StatisticRepository $statisticRepo,
        PrinterJobRepository $printerJobRepo,
        CategoryRelationRepository $categoryRelationRepository
    ) {
        parent::__construct();
        
        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->categoryOptionRepository = $categoryOptionRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->productLabelRepository = $productLabelRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->vatRepository = $vatRepository;
        $this->vatRepository = $vatRepository;
        $this->optionItemRepository = $optionItemRepository;
        $this->groupRepository = $groupRepository;
        $this->groupProductRepository = $groupProductRepository;
        $this->workspaceRepository = $workspaceRepo;
        $this->statisticRepository = $statisticRepo;
        $this->printerJobRepository = $printerJobRepo;
        $this->categoryRelationRepository = $categoryRelationRepository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');

        $all = $request->all();
        $request->request->add([
            'workspace_id' => $this->tmpUser->workspace_id,
            'sort_by' => array_get($all, 'sort_by', "created_at"),
            'order_by' => array_get($all, 'order_by', "desc"),
            'managerWeb' => true
        ]);

        $groups = $this->groupRepository->paginate(20, ['*'], 'paginate', ['workspace']);
        return view('manager.groups.index', compact('groups'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);
        
        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);
        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = [];
        
        return response()->json([
            'code' => 200,
            'data' => view('manager.groups.partials.form', [
                'action' => route($this->guard.'.groups.store'),
                'method' => 'POST',
                'idForm' => 'create_group',
                'titleModal' => trans('group.nieuwe_ategorie'),
                'productsGroupByCategory' => $productsGroupByCategory,
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateGroupRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateGroupRequest $request)
    {
        try {
            \DB::beginTransaction();
            
            $data = $request->all();
            
            if ($request->payment_mollie === 0 && $request->payment_payconiq === 0
                && $request->payment_cash === 0 && $request->payment_factuur === 0
            ) {
                return $this->sendError("payment_method_error", 422, [
                    "payment_method_error" => trans('group.validation.method_payment_required')
                ]);
            }
            
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            
            $group = $this->groupRepository->create($data);
            
            $timeSlots = array();
            foreach ($data['days'] as $k => $day) {
                $day['foreign_model'] = Group::class;
                $day['workspace_id'] = $group->workspace_id;
                $day['foreign_id'] = $group->id;
                $day['status'] = isset($day['status']);
                $timeSlots[$k] = $day;
            }
            
            $this->openTimeslotRepository->saveMany($timeSlots);
            
            $groupProducts = [];
            $listChoosenProduct = array_filter(explode(',', $data['listProduct']));
            foreach ($listChoosenProduct as $k => $id) {
                $groupProducts[$k]['group_id'] = $group->id;
                $groupProducts[$k]['product_id'] = (int) $id;
            }
            
            $this->groupProductRepository->saveMany($groupProducts);
            
            //Save categories
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Group::class,
                    $group->id);
            }
            
            \DB::commit();
            
            if ($request->ajax()) {
                return $this->sendResponse($data, trans('group.created_successfully'));
            }
            
            Flash::success(trans('group.created_successfully'));
            
        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());
            
            return response()->json([
                'code' => 500,
                'message' => $exc,
            ]);
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param  int  $groupId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $groupId)
    {
        $group = $this->groupRepository->findWithoutFail($groupId);
        
        if (empty($group)) {
            Flash::error(trans('group.not_found'));
            return redirect(route($this->guard.'.groups.index'));
        }
        
        $openTime = $this->openTimeslotRepository->findWhere([
            'foreign_model' => Group::class,
            'foreign_id' => $group->id,
        ])->keyBy('day_number')->toArray();
        
        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);
        
        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);
        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = $group->categoriesRelation()->pluck('category_id')->toArray();
        
        return response()->json([
            'code' => 200,
            'data' => view('manager.groups.partials.form', [
                'group' => $group,
                'openTime' => $openTime,
                'action' => route($this->guard.'.groups.update', $group->id),
                'method' => 'PUT',
                'idForm' => 'update_group',
                'titleModal' => trans('group.categorie_wijzigen'),
                'productsGroupByCategory' => $productsGroupByCategory,
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateGroupRequest  $request
     * @param  int  $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGroupRequest $request, int $groupId)
    {
        $groupCurrent = $group = $this->groupRepository->findWithoutFail($groupId);
        
        if (empty($group)) {
            Flash::error(trans('group.not_found'));
            return redirect(route($this->guard.'.groups.index'));
        }
        
        if ($request->payment_mollie === 0 && $request->payment_payconiq === 0
            && $request->payment_cash === 0 && $request->payment_factuur === 0
        ) {
            return $this->sendError("payment_method_error", 422, [
                "payment_method_error" => trans('group.validation.method_payment_required')
            ]);
        }
        
        try {
            \DB::beginTransaction();
            
            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $group = $this->groupRepository->update($data, $groupId);
            
            if ($groupCurrent->receive_time != $data['receive_time']) {
                $this->groupRepository->updateOrderWhenChangeTime($group);
            }
            
            $timeSlots = array();
            foreach ($data['days'] as $k => $day) {
                $day['foreign_model'] = Group::class;
                $day['workspace_id'] = $group->workspace_id;
                $day['foreign_id'] = $group->id;
                $day['status'] = isset($day['status']);
                $timeSlots[$k] = $day;
            }
            
            $this->openTimeslotRepository->deleteWhere([
                'foreign_model' => Group::class,
                'foreign_id' => $group->id,
            ]);
            
            $this->openTimeslotRepository->saveMany($timeSlots);
            
            $groupProducts = [];
            $listChoosenProduct = array_filter(explode(',', $data['listProduct']));
            foreach ($listChoosenProduct as $k => $id) {
                $groupProducts[$k]['group_id'] = $group->id;
                $groupProducts[$k]['product_id'] = (int) $id;
            }
            
            $this->groupProductRepository->deleteWhere(['group_id' => $groupId]);
            $this->groupProductRepository->saveMany($groupProducts);
            
            //Save categories
            $this->categoryRelationRepository->deleteWhere(['foreign_model' => Group::class, 'foreign_id' => $groupId]);
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Group::class,
                    $groupId);
            }
            
            \DB::commit();
            
            if ($request->ajax()) {
                return $this->sendResponse($data, trans('group.updated_successfully'));
            }
            
            Flash::success(trans('group.updated_successfully'));
            
        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());
            
            return response()->json([
                'code' => 500,
                'message' => $exc,
            ]);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $group = $this->groupRepository->findWithoutFail($id);
        
        if (empty($group)) {
            Flash::error(trans('group.not_found'));
            return redirect(route($this->guard.'.groups.index'));
        }
        
        if (!Helper::isSuperAdmin()) {
            Flash::error(trans('group.forbidden'));
            return response()->json([
                'status' => 'error',
                'message' => trans('group.forbidden'),
            ], 403);
        }
        
        $this->groupRepository->delete($id);
        
        return response()->json([
            'status' => 'success',
            'message' => trans('group.deleted_confirm'),
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $group = $this->groupRepository->find($id);
        
        if (empty($group)) {
            Flash::error(trans('user.not_found'));
            
            return redirect(route($this->guard.'.managers.index'));
        }
        
        $group->active = !$group->active;
        $group->save();
        
        $response = array(
            'data' => $group->toArray(),
            'status' => $group->active,
        );
        
        return response()->json($response);
    }
    
    /**
     * @param $id
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Throwable
     */
    public function perProduct($id, Request $request)
    {
        $group = $this->groupRepository->find($id);
        
        if (empty($group)) {
            Flash::error(trans('user.not_found'));
            
            return redirect(route($this->guard.'.managers.index'));
        }
        
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceIds = [$this->tmpWorkspace->id];
        $autoloadAjax = 1;
        
        if (empty($rangeStartDate) || empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);
            
            if (!empty($rangeStartDate)) {
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)).' '.'00:00:00';
            }
            if (!empty($rangeEndDate)) {
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)).' '.'23:59:59';
            }
        }
        
        $productIds = $this->getProductByKeyword($keyword);
        $discounts = $this->statisticRepository->statisticManagerPerProduct(
            $rangeStartDate,
            $rangeEndDate,
            $timezone,
            $keyword,
            $workspaceIds,
            false,
            [],
            $productIds,
            $group->id
        );

        $perProducts = $this->statisticRepository->groupByCategory($discounts, false, !empty($keyword), $productIds);
        $hasShipOrders = $discounts->where('ship_price', '>', 0);
        $serviceCost = $discounts->where('service_cost', '>', 0);
        $calculateServiceCost = [
            'amount' => $serviceCost->count() ?? 0,
            'total_revenue' => $serviceCost->sum('service_cost') ?? 0
        ];
        $totalInclDiscount = 0;
        $totalDiscount = 0;
        $viewData = compact(
            'group',
            'perProducts',
            'totalDiscount',
            'totalInclDiscount',
            'hasShipOrders',
            'keyword',
            'autoloadAjax',
            'calculateServiceCost'
        );
        
        if ($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_PER_PRODUCT, $viewData);
            
            return $this->sendResponse(null, 'success');
        }
        
        if ($request->ajax()) {
            $view = view($this->guard.'.groups.statistic.partials.per_product_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }
        
        return view($this->guard.'.groups.statistic.per_product', $viewData);
    }
    
    /**
     * @param $id
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Throwable
     */
    public function discount($id, Request $request)
    {
        $group = $this->groupRepository->find($id);
        
        if (empty($group)) {
            Flash::error(trans('user.not_found'));
            
            return redirect(route($this->guard.'.managers.index'));
        }
        
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceId = $this->tmpWorkspace->id;
        $autoloadAjax = 1;
        $workspaceIds = [$workspaceId];
        $vats = Vat::getVats($this->tmpWorkspace->country_id);
        
        if (empty($rangeStartDate) || empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);
            
            if (!empty($rangeStartDate)) {
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)).' '.'00:00:00';
            }
            if (!empty($rangeEndDate)) {
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)).' '.'23:59:59';
            }
        }
        
        $productIds = $this->getProductByKeyword($keyword);
        $discounts = $this->statisticRepository->statisticManagerPerProduct(
            $rangeStartDate,
            $rangeEndDate,
            $timezone,
            $keyword,
            $workspaceIds,
            false,
            [],
            $productIds,
            $group->id
        );
        
        $perProducts = $this->statisticRepository->groupByCategory($discounts, true, !empty($keyword), $productIds);
        $perProducts = collect($perProducts)->where('cat_price', '>', 0);
        $totalDiscount = collect($perProducts)->sum('cat_price');
        
        $rewards = RedeemHistory::getListByWorkspace($workspaceId, $rangeStartDate, $rangeEndDate);
        $totalReward = collect($rewards)->sum('totalReward');
        
        $viewData = compact(
            'group',
            'perProducts',
            'totalDiscount',
            'vats',
            'rewards',
            'totalReward',
            'autoloadAjax'
        );
        
        if ($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_DISCOUNT, $viewData);
            
            return $this->sendResponse(null, 'success');
        }
        
        if ($request->ajax()) {
            $view = view($this->guard.'.groups.statistic.partials.discount_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }
        
        return view($this->guard.'.groups.statistic.discount', $viewData);
    }
    
    /**
     * @param $id
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Throwable
     */
    public function perPaymentMethod($id, Request $request)
    {
        $group = $this->groupRepository->find($id);
        
        if (empty($group)) {
            Flash::error(trans('user.not_found'));
            
            return redirect(route($this->guard.'.managers.index'));
        }
        
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceId = $this->tmpWorkspace->id;
        $autoloadAjax = 1;
        $workspaceIds = [$workspaceId];
        $vats = Vat::getVats($this->tmpWorkspace->country_id);
        
        if (empty($rangeStartDate) || empty($rangeEndDate)) {
            $today = date('Y-m-d');
            $rangeStartDate = $today.' '.'00:00:00';
            $rangeEndDate = $today.' '.'23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);
            
            if (!empty($rangeStartDate)) {
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)).' '.'00:00:00';
            }
            if (!empty($rangeEndDate)) {
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)).' '.'23:59:59';
            }
        }
        
        $discounts = $this->statisticRepository->statisticManagerPerProduct($rangeStartDate, $rangeEndDate, $timezone,
            $keyword, $workspaceIds, false, [], [], $group->id);
        $perProducts = $this->statisticRepository->groupByPaymentMethod($discounts);
        $totalDiscount = collect($perProducts)->sum('discount_price');
        $totalCart = collect($perProducts)->sum('payment_total');
        $totalInclDiscount = collect($perProducts)->sum('payment_price');
        ksort($perProducts);
        $viewData = compact(
            'group',
            'perProducts',
            'totalDiscount',
            'totalInclDiscount',
            'vats',
            'totalCart',
            'autoloadAjax'
        );
        
        if ($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_PER_PAYMENT_METHOD,
                $viewData);
            
            return $this->sendResponse(null, 'success');
        }
        
        if ($request->ajax()) {
            $view = view($this->guard.'.groups.statistic.partials.per_payment_method_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }
        
        return view($this->guard.'.groups.statistic.per_payment_method', $viewData);
    }
    
    private function bonPrinter($workspaceId, $type, $viewData)
    {
        $width = config('print.px.werkbon.width');
        $viewData = array_merge($viewData, compact('width'));
        $view = view($this->guard.'.groups.statistic.bon_printer.'.$type, $viewData)->render();
        $imageName = implode('-', [$type, $workspaceId, strtotime(now()).'.png']);
        $path = implode('/', [config('filesystems.disks.public.root'), 'print', $imageName]);
        
        \SnappyImage::loadHTML($view)
            ->setOption('width', $width)
            ->setOption('quality', 1)
            ->setOption('format', 'png')
            ->setOption('disable-smart-width', true)
            ->save($path);
        
        $data = $this->prepareStatisticJobData($workspaceId, $type, $imageName);
        
        \App\Helpers\Order::createJobAndCopyPrint($data, false);
    }
    
    private function prepareStatisticJobData($workspaceId, $type, $image)
    {
        $data = [];
        $now = now();
        $printPath = 'print';
        $printPartPath = 'print/parts';
        $imagePath = implode('/', [$printPath, $image]);
        $sourceFile = public_path('storage/print/'.$image);
        $destPath = implode('/', [config('filesystems.disks.public.root'), $printPartPath]);
        
        $helper = new Helper();
        $imageSplits = $helper->splitImage($sourceFile, $destPath, basename($image, '.png').'-part%02d.png');
        $metaData = [];
        
        if (!empty($imageSplits)) {
            foreach ($imageSplits as $imageItem) {
                $metaData[] = [
                    'type' => 'image',
                    'filename' => $imageItem,
                    'path' => implode('/', [$printPartPath, $imageItem]),
                    'printed' => 0
                ];
            }
        }
        
        $data[] = [
            'workspace_id' => $workspaceId,
            'status' => \App\Models\PrinterJob::STATUS_PENDING,
            'job_type' => config('print.job_type.werkbon'),
            'foreign_model' => $type,
            'foreign_id' => null,
            'content' => $imagePath,
            'meta_data' => !empty($metaData) ? json_encode($metaData) : null,
            'created_at' => $now,
            'updated_at' => $now
        ];
        
        return $data;
    }
    
    private function getProductByKeyword($keyword)
    {
        $productIds = [];
        
        if (!empty($keyword)) {
            $products = ProductTranslation::where('name', 'LIKE', '%'.$keyword.'%')
                ->where('locale', app()->getLocale())
                ->groupBy('product_id')
                ->get();
            
            if (!$products->isEmpty()) {
                $productIds = $products->pluck('product_id')->all();
            }
        }
        
        return $productIds;
    }
}
