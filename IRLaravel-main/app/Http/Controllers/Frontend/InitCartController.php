<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Cart;
use App\Repositories\CartItemRepository;
use App\Repositories\CartOptionItemRepository;
use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;
use Log;
use Webpatser\Uuid\Uuid;

class InitCartController extends BaseController
{
    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * @var WorkspaceRepository
     */
    public $workspaceRepository;

    /**
     * @var CartItemRepository
     */
    public $cartItemRepository;

    /**
     * @var CartOptionItemRepository
     */
    public $cartOptionItemRepository;

    /**
     * @var CategoryRepository
     */
    public $categoryRepository;

    /**
     * InitCartController constructor.
     *
     * @param CartRepository           $cartRepository
     * @param WorkspaceRepository      $workspaceRepository
     * @param CartItemRepository       $cartItemRepository
     * @param CartOptionItemRepository $cartOptionItemRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        WorkspaceRepository $workspaceRepository,
        CartItemRepository $cartItemRepository,
        CartOptionItemRepository $cartOptionItemRepository,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartOptionItemRepository = $cartOptionItemRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $param = $request->type == Cart::TYPE_LEVERING ? Cart::TAB_LEVERING : Cart::TAB_TAKEOUT;
        // if ($param === Cart::TAB_LEVERING && isset($request->group_id) && !empty($request->group_id)) {
        //     $param = Cart::TAB_TAKEOUT;
        // }

        if (isset($request->switch_tab)) {
            $param = Cart::TAB_LEVERING;
        }

        // Update flow new CR
        // if (auth()->guest()) {
        //     $param = Cart::TAB_TAKEOUT;
        //     $url = route('web.user.index', [$request->workspace_id]) . "?tab=" . $param;
        //     return redirect($url);
        // }

        try {
            \DB::beginTransaction();

            // Fake id for user
            if (!$request->user_id) {
                $maskUserId = Uuid::generate()->string;
                $request->request->add(['user_id' => $maskUserId]);
                session(['user_id_not_login' => $maskUserId]);
            }

            $inputs = $request->toArray();

            if (isset($request->address_type)) {

                if ($request->address_type == 0 && !auth()->guest()) {
                    $inputs['lat']     = auth()->user()->lat;
                    $inputs['long']    = auth()->user()->lng;
                    $inputs['address'] = auth()->user()->address;
                }

                $condition = $this->workspaceRepository->getDeliveryConditions($request->workspace_id, [
                    'lat'  => $inputs['lat'],
                    'lng'  => $inputs['long'],
                ])->first();

                session(['condition' => $condition]);
                session()->flash('address_type', 1);

                if (!$condition) {
                    session()->flash('address_not_avaliable', 1);
                    return redirect(route('web.category.index'));
                }
            }

            if ((isset($request->type) && $request->type == Cart::TYPE_LEVERING) || (isset($request->group_id))) {
                session(['orderType' => $inputs]);
            } else {
                session()->forget('orderType');
            }

            $this->cartRepository->updateOrCreate([
                'user_id'      => $request->user_id,
                'workspace_id' => $request->workspace_id,
            ], $inputs);

            $firstCategory = $this->categoryRepository->getFirstCategory($request->workspace_id, $param);
            $url = route('web.user.index') . "?tab=" . $param;
            if (!empty($firstCategory)) {
                $url = route('web.user.index', [$firstCategory->id]) . "?tab=" . $param;
            }

            session('urlType', $url);

            \DB::commit();
            return redirect($url);

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error("MESSAGE: " . $exc->getMessage());
            Log::error("ERROR: " . $exc->getTraceAsString());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeWithoutLogin(Request $request)
    {
        try {
            \DB::beginTransaction();

            $workspace = $this->workspaceRepository->findWithoutFail($request->workspaceId);

            if (!session()->has('cart_without_login_'.$workspace->slug) || session()->get('cart_without_login_'.$workspace->slug)->count() === 0) {
                return response()->json([
                    'code' => 200,
                    'data' => NULL
                ]);
            }

            $cart = session()->get('cart_without_login_'.$workspace->slug);

            $this->cartRepository->deleteWhere([
                'user_id'      => $request->userId,
                'workspace_id' => $request->workspaceId
            ]);

            $newCartItem = \App\Helpers\Helper::handleCartWithoutLogin($cart);

            unset($cart['id']);
            $cart['user_id'] = $request->userId;

            $objCart = $this->cartRepository->create($cart);

            foreach ($newCartItem as $cartItem) {
                unset($cartItem['id']);
                $cartItem['cart_id'] = $objCart->id;
                $objCartItem = $this->cartItemRepository->create($cartItem);

                foreach ($cartItem['cart_option_items'] as $optItem) {
                    unset($optItem['id']);
                    unset($optItem['created_at']);
                    unset($optItem['updated_at']);
                    $optItem['cart_item_id'] = $objCartItem->id;
                    $this->cartOptionItemRepository->create($optItem);
                }
            }

            \DB::commit();

            session()->forget('cart_without_login_'.$workspace->slug);

            return response()->json([
                'code' => 200,
                'data' => $cart
            ]);

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error("MESSAGE: " . $exc->getMessage());
            Log::error("ERROR: " . $exc->getTraceAsString());

            return response()->json([
                'code'    => 500,
                'message' => $exc->getMessage()
            ]);
        }
    }
}
