<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Repositories\CategoryRepository;
use App\Repositories\CouponCategoryRepository;
use App\Repositories\CouponProductRepository;
use App\Repositories\CouponRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WorkspaceExtraRepository;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\CategoryRelationRepository;
use Log;

/**
 * Class CouponController
 *
 * @package App\Http\Controllers\Manager
 */
class CouponController extends BaseController
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
     * @var CouponProductRepository
     */
    private $couponProductRepository;

    /**
     * @var CouponCategoryRepository
     */
    private $couponCategoryRepository;

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
     * @var ProductAllergenenRepository
     */
    private $productAllergenenRepository;

    /**
     * @var CouponRepository
     */
    private $couponRepository;

    /**
     * @var WorkspaceExtraRepository
     */
    private $workspaceExtraRepository;

    private $categoryRelationRepository;

    /**
     * CouponController constructor.
     * @param CategoryRepository $categoryRepository
     * @param OptionRepository $optionRepository
     * @param CouponProductRepository $couponProductRepository
     * @param CouponCategoryRepository $couponCategoryRepository
     * @param ProductAllergenenRepository $productAllergenenRepository
     * @param OpenTimeslotRepository $openTimeslotRepository
     * @param MediaRepository $mediaRepository
     * @param ProductRepository $productRepository
     * @param CouponRepository $couponRepository
     * @param WorkspaceExtraRepository $workspaceExtraRepository
     * @param CategoryRelationRepository $categoryRelationRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        CouponProductRepository $couponProductRepository,
        CouponCategoryRepository $couponCategoryRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        CouponRepository $couponRepository,
        WorkspaceExtraRepository $workspaceExtraRepository,
        CategoryRelationRepository $categoryRelationRepository
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->couponProductRepository = $couponProductRepository;
        $this->couponCategoryRepository = $couponCategoryRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->couponRepository = $couponRepository;
        $this->workspaceExtraRepository = $workspaceExtraRepository;
        $this->categoryRelationRepository = $categoryRelationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request->request->add([
            'workspace_id' => $this->tmpUser->workspace_id,
            'count_orders' => true,
        ]);

        $coupons = $this->couponRepository->paginate(20);

        return view('manager.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $products = $this->productRepository->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);
        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = [];

        return response()->json([
            'code' => 200,
            'data' => view('manager.coupons.partials.form', [
                'products'                => $products,
                'productsGroupByCategory' => $productsGroupByCategory,
                'action'                  => route($this->guard . '.coupons.store'),
                'method'                  => 'POST',
                'idForm'                  => 'create_coupon',
                'titleModal'              => trans('coupon.nieuwe_ategorie'),
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCouponRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCouponRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['expire_time']  = Helper::convertDateTimeToUTC($data['expire_time'], $request->timeZone)->format('Y-m-d H:i:s');

            $coupon = $this->couponRepository->create($data);

            $couponProducts = array();
            $listChoosenProduct = array_filter(explode(',', $data['listProduct']));
            foreach ($listChoosenProduct as $k => $id) {
                $couponProducts[$k]['coupon_id']  = $coupon->id;
                $couponProducts[$k]['product_id'] = (int) $id;
            }

            $this->couponProductRepository->saveMany($couponProducts);

            //Save categories
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Coupon::class, $coupon->id);
            }

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('coupon.created_successfully'));
            }

            Flash::success(trans('coupon.created_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getTraceAsString());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int     $couponId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $couponId)
    {
        $coupon = $this->couponRepository->with(['categories', 'products'])->findWithoutFail($couponId);

        if (empty($coupon)) {
            Flash::error(trans('coupon.not_found'));
            return redirect(route($this->guard . '.coupons.index'));
        }

        $products = $this->productRepository->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $productsGroup = $this->categoryRepository
            ->with('products')
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);
        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = $coupon->categoriesRelation()->pluck('category_id')->toArray();

        return response()->json([
            'code' => 200,
            'data' => view('manager.coupons.partials.form', [
                'products'                => $products,
                'coupon'                  => $coupon,
                'productsGroupByCategory' => $productsGroupByCategory,
                'action'                  => route($this->guard . '.coupons.update', $coupon->id),
                'method'                  => 'PUT',
                'idForm'                  => 'update_coupon',
                'titleModal'              => trans('coupon.categorie_wijzigen'),
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCouponRequest $request
     * @param int                  $couponId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCouponRequest $request, int $couponId)
    {
        $coupon = $this->couponRepository->findWithoutFail($couponId);

        if (empty($coupon)) {
            Flash::error(trans('coupon.not_found'));
            return redirect(route($this->guard . '.coupons.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['expire_time']  = Carbon::parse($data['expire_time'])->format('Y-m-d H:i:s');

            $coupon = $this->couponRepository->update($data, $couponId);

            $couponProducts = array();
            $listChoosenProduct = array_filter(explode(',', $data['listProduct']));
            foreach ($listChoosenProduct as $k => $id) {
                $couponProducts[$k]['coupon_id']  = $coupon->id;
                $couponProducts[$k]['product_id'] = (int) $id;
            }

            $this->couponProductRepository->deleteWhere(['coupon_id' => $couponId]);
            $this->couponProductRepository->saveMany($couponProducts);

            //Save categories
            $this->categoryRelationRepository->deleteWhere(['foreign_model' => Coupon::class, 'foreign_id' => $coupon->id]);
            if (!empty($request->category_ids)) {
                $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Coupon::class, $coupon->id);
            }

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('coupon.updated_successfully'));
            }

            Flash::success(trans('coupon.updated_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
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
        $coupon = $this->couponRepository->findWithoutFail($id);

        if (empty($coupon)) {
            Flash::error(trans('coupon.not_found'));
            return redirect(route($this->guard . '.coupons.index'));
        }

        $this->couponRepository->delete($id);
        $this->categoryRelationRepository->deleteWhere(['foreign_model' => Coupon::class, 'foreign_id' => $coupon->id]);

        return response()->json([
            'status'  => 'success',
            'message' => trans('coupon.deleted_confirm'),
        ]);
    }
}
