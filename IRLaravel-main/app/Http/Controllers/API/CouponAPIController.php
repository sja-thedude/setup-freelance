<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCouponAPIRequest;
use App\Http\Requests\API\UpdateCouponAPIRequest;
use App\Http\Requests\API\ValidateCouponAPIRequest;
use App\Models\Coupon;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CouponController
 * @package App\Http\Controllers\API
 */
class CouponAPIController extends AppBaseController
{
    /** @var CouponRepository $couponRepository */
    protected $couponRepository;

    /**
     * CouponAPIController constructor.
     * @param CouponRepository $couponRepo
     */
    public function __construct(CouponRepository $couponRepo)
    {
        parent::__construct();

        $this->couponRepository = $couponRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Custom request
        $request->merge([
            'active' => true,
            'hide_expire_time' => true,
            'hide_max_time_all' => true,
        ]);

        try {
            $this->couponRepository->pushCriteria(new RequestCriteria($request));
            $this->couponRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $coupons = $this->couponRepository->paginate($limit);

        $coupons->transform(function ($item) {
            /** @var \App\Models\Coupon $item */
            return $item->getFullInfo();
        });
        $result = $coupons->toArray();

        return $this->sendResponse($result, trans('coupon.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateCouponAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCouponAPIRequest $request)
    {
        $input = $request->all();

        $coupon = $this->couponRepository->create($input);

        return $this->sendResponse($coupon->toArray(), trans('coupon.saved_successfully'));
    }

    /**
     * @param Coupon $coupon
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Coupon $coupon)
    {
        $result = $coupon->getFullInfo();

        // Attach relations
        $result = array_merge($result, [
            'categories' => $coupon->categories->transform(function ($category) {
                /** @var \App\Models\Category $category */
                return $category->getSummaryInfo();
            }),
            'products' => $coupon->products->transform(function ($product) {
                /** @var \App\Models\Product $product */
                return $product->getSummaryInfo();
            }),
        ]);

        return $this->sendResponse($result, trans('coupon.message_retrieved_successfully'));
    }

    /**
     * @param UpdateCouponAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCouponAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Coupon $coupon */
        $coupon = $this->couponRepository->findWithoutFail($id);

        if (empty($coupon)) {
            return $this->sendError(trans('coupon.not_found'));
        }

        $coupon = $this->couponRepository->update($input, $id);

        return $this->sendResponse($coupon->toArray(), trans('coupon.updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Coupon $coupon */
        $coupon = $this->couponRepository->findWithoutFail($id);

        if (empty($coupon)) {
            return $this->sendError(trans('coupon.not_found'));
        }

        $coupon->delete();

        return $this->sendResponse($id, trans('coupon.deleted_successfully'));
    }

    /**
     * Validate a coupon code
     *
     * @param ValidateCouponAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCode(ValidateCouponAPIRequest $request)
    {
        $code = $request->get('code');
        $workspaceId = $request->get('workspace_id');
        $coupon = $this->couponRepository->validateCode($workspaceId, $code);

        // Is valid
        if (!empty($coupon)) {
            $result = $coupon->getFullInfo();

            // Attach relations
            $result = array_merge($result, [
                'categories' => $coupon->categories->transform(function ($category) {
                    /** @var \App\Models\Category $category */
                    return $category->getSummaryInfo();
                }),
                'products' => $coupon->products->transform(function ($product) {
                    /** @var \App\Models\Product $product */
                    return $product->getSummaryInfo();
                }),
            ]);

            return $this->sendResponse($result, trans('coupon.message_code_valid'));
        }

        return $this->sendError(trans('coupon.message_code_invalid'), 422);
    }

}
