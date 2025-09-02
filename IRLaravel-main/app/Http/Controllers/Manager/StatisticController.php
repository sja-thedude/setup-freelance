<?php

namespace App\Http\Controllers\Manager;

use App\Models\PrinterJob;
use App\Models\Product;
use App\Models\RedeemHistory;
use App\Models\Vat;
use Illuminate\Http\Request;
use App\Repositories\WorkspaceRepository;
use App\Repositories\StatisticRepository;
use App\Repositories\PrinterJobRepository;
use App\Repositories\SettingPreferenceRepository;
use App\Repositories\ServiceCostRepository;
use App\Facades\Helper;

class StatisticController extends BaseController
{
    private $workspaceRepository;
    private $statisticRepository;
    private $printerJobRepository;
    private $settingPreferenceRepository;

    /**
     * StatisticController constructor.
     * @param WorkspaceRepository $workspaceRepo
     * @param StatisticRepository $statisticRepo
     * @param PrinterJobRepository $printerJobRepo
     */
    public function __construct(
        WorkspaceRepository $workspaceRepo,
        StatisticRepository $statisticRepo,
        PrinterJobRepository $printerJobRepo,
        SettingPreferenceRepository $settingPreferenceRepo
    ) {
        // This needs more memory for bigger data (we can't lower this memory usage)
        // ini_set('memory_limit','1024M');

        parent::__construct();

        $this->workspaceRepository = $workspaceRepo;
        $this->statisticRepository = $statisticRepo;
        $this->printerJobRepository = $printerJobRepo;
        $this->settingPreferenceRepository = $settingPreferenceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function perProduct(Request $request)
    {
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceIds = [$this->tmpWorkspace->id];
        $autoloadAjax = 1;

        if(empty($rangeStartDate) || empty($rangeEndDate)){
            $today = date('Y-m-d');
            $rangeStartDate = $today . ' ' . '00:00:00';
            $rangeEndDate = $today . ' ' . '23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);

            if(!empty($rangeStartDate)){
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)) . ' ' . '00:00:00';
            }
            if(!empty($rangeEndDate)){
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)) . ' ' . '23:59:59';
            }
        }

        $productIds = $this->getProductByKeyword($keyword, $workspaceIds);
        $discounts = $this->statisticRepository->statisticManagerPerProduct(
            $rangeStartDate,
            $rangeEndDate,
            $timezone,
            $keyword,
            $workspaceIds,
            false,
            [],
            $productIds
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
            'perProducts',
            'totalDiscount',
            'totalInclDiscount',
            'hasShipOrders',
            'keyword',
            'autoloadAjax',
            'calculateServiceCost'
        );

        if($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_PER_PRODUCT, $viewData);

            return $this->sendResponse(null, 'success');
        }

        if($request->ajax()) {
            $view = view($this->guard.'.statistic.partials.per_product_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }

        return view($this->guard.'.statistic.per_product', $viewData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function discount(Request $request)
    {
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceId = $this->tmpWorkspace->id;
        $autoloadAjax = 1;
        $workspaceIds = [$workspaceId];
        $vats = Vat::getVats($this->tmpWorkspace->country_id);

        if(empty($rangeStartDate) || empty($rangeEndDate)){
            $today = date('Y-m-d');
            $rangeStartDate = $today . ' ' . '00:00:00';
            $rangeEndDate = $today . ' ' . '23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);

            if(!empty($rangeStartDate)){
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)) . ' ' . '00:00:00';
            }
            if(!empty($rangeEndDate)){
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)) . ' ' . '23:59:59';
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
            $productIds
        );

        $perProducts = $this->statisticRepository->groupByCategory($discounts, true, !empty($keyword), $productIds);
        $perProducts = collect($perProducts)->where('cat_price', '>', 0);
        $totalDiscount = collect($perProducts)->sum('cat_price');

        $rewards = RedeemHistory::getListByWorkspace($workspaceId, $rangeStartDate, $rangeEndDate);
        $totalReward = collect($rewards)->sum('totalReward');

        $viewData = compact(
            'perProducts',
            'totalDiscount',
            'vats',
            'rewards',
            'totalReward',
            'autoloadAjax'
        );

        if($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_DISCOUNT, $viewData);

            return $this->sendResponse(null, 'success');
        }

        if($request->ajax()) {
            $view = view($this->guard.'.statistic.partials.discount_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }

        return view($this->guard.'.statistic.discount', $viewData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function perPaymentMethod(Request $request) {
        $rangeStartDate = $request->get('start_time', null);
        $rangeEndDate = $request->get('end_time', null);
        $keyword = $request->get('keyword_search', null);
        $timezone = 'UTC';
        $workspaceId = $this->tmpWorkspace->id;
        $autoloadAjax = 1;
        $workspaceIds = [$workspaceId];
        $vats = Vat::getVats($this->tmpWorkspace->country_id);

        if(empty($rangeStartDate) || empty($rangeEndDate)){
            $today = date('Y-m-d');
            $rangeStartDate = $today . ' ' . '00:00:00';
            $rangeEndDate = $today . ' ' . '23:59:59';
        } else {
            $autoloadAjax = 0;
            $timezone = $request->get('timezone', $timezone);

            if(!empty($rangeStartDate)){
                $rangeStartDate = date('Y-m-d', strtotime($rangeStartDate)) . ' ' . '00:00:00';
            }
            if(!empty($rangeEndDate)){
                $rangeEndDate = date('Y-m-d', strtotime($rangeEndDate)) . ' ' . '23:59:59';
            }
        }

        $discounts = $this->statisticRepository->statisticManagerPerProduct($rangeStartDate, $rangeEndDate, $timezone, $keyword, $workspaceIds);
        $perProducts = $this->statisticRepository->groupByPaymentMethod($discounts);
        $totalDiscount = collect($perProducts)->sum('discount_price');
        $totalCart = collect($perProducts)->sum('payment_total');
        $totalInclDiscount = collect($perProducts)->sum('payment_price');
        ksort($perProducts);
        $viewData = compact(
            'perProducts',
            'totalDiscount',
            'totalInclDiscount',
            'vats',
            'totalCart',
            'autoloadAjax'
        );

        if($request->has('bon_printer')) {
            $viewData['filterDate'] = $request->get('filter_date', null);
            $this->bonPrinter($this->tmpWorkspace->id, PrinterJob::FOREIGN_MODEL_STATISTIC_PER_PAYMENT_METHOD, $viewData);

            return $this->sendResponse(null, 'success');
        }

        if($request->ajax()) {
            $view = view($this->guard.'.statistic.partials.per_payment_method_table', $viewData)->render();
            return $this->sendResponse(compact('view'), 'success');
        }

        return view($this->guard.'.statistic.per_payment_method', $viewData);
    }

    private function bonPrinter($workspaceId, $type, $viewData) {
        $width = config('print.px.werkbon.width');
        $viewData = array_merge($viewData, compact('width'));
        $view = view($this->guard.'.statistic.bon_printer.'.$type, $viewData)->render();
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

    private function prepareStatisticJobData($workspaceId, $type, $image) {
        $data = [];
        $now = now();
        $printPath = 'print';
        $printPartPath = 'print/parts';
        $imagePath = implode('/', [$printPath, $image]);
        $sourceFile = public_path('storage/print/'.$image);
        $destPath = implode('/', [config('filesystems.disks.public.root'), $printPartPath]);
        $imageSplits = Helper::splitImage($sourceFile, $destPath, basename($image, '.png').'-part%02d.png');
        $metaData = [];

        if(!empty($imageSplits)) {
            foreach($imageSplits as $imageItem) {
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

    private function getProductByKeyword($keyword, array $workspaceIds = null)
    {
        $productIds = [];

        if (!empty($keyword)) {
            $products = Product::join('product_translations AS t_products', 'products.id', '=', 't_products.product_id')
                ->where('t_products.name', 'LIKE', '%' . $keyword . '%')
                ->where('t_products.locale', app()->getLocale())
                ->groupBy('products.id');

            if (!empty($workspaceIds)) {
                $products = $products->whereIn('products.workspace_id', $workspaceIds);
            }

            $products = $products->get();

            if (!$products->isEmpty()) {
                $productIds = $products->pluck('product_id')->all();
            }
        }

        return $productIds;
    }
}
