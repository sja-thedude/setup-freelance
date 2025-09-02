<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\CouponRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

/**
 * Class CouponController
 *
 * @package App\Http\Controllers\Manager
 */
class CouponController extends BaseController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CouponRepository
     */
    private $couponRepository;

    /**
     * CouponController constructor.
     *
     * @param ProductRepository $productRepository
     * @param CouponRepository  $couponRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        CouponRepository $couponRepository
    ) {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param Request $request
     * @param         $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show(Request $request, $code)
    {
        try {
            $productIds = explode(',', $request->product_id);
            $code       = base64_decode($code);
            $host = $request->getHost();
            $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
            $workspace = session('workspace_'.$workspaceSlug);
            $workspaceId = null;
            if ($workspace) {
                $workspaceId = $workspace->id;
            }
            $result = $this->productRepository->validateProductCoupon($productIds, $code, $request->user_id, $workspaceId);

            if (!$result || !in_array(true, $result)) {
                return response()->json([
                    'code'    => 422,
                    'message' => trans('cart.korting_is_niet_van')
                ]);
            }

            $coupon = $this->couponRepository->findWhere(['code' => $code, 'workspace_id' => $workspaceId])->first();
            $cartId = $request->get('cart_id');
            $products = [];
            foreach ($result as $productId => $value) {
                if ($value == true) {
                    $products[] = $productId;
                }
            }
            $discountCouponProducts = $this->productRepository->getMinCouponDiscountPrice($cartId, $products, $coupon);
            $couponData = $coupon ? $coupon->toArray() : [];
            $couponData = array_merge($couponData, $discountCouponProducts);

            return response()->json([
                'code' => 200,
                'data' => $couponData
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'code'    => 422,
                'message' => $ex->getMessage()
            ]);
        }
    }
}
