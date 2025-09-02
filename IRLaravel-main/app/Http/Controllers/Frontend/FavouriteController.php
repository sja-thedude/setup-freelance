<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Cart;
use App\Models\Group;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Repositories\CartRepository;

class FavouriteController extends BaseController
{
    private $productRepository;
    
    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * FavouriteController constructor.
     * @param ProductRepository $productRepo
     * @param CartRepository $cartRepository
     */
    public function __construct(ProductRepository $productRepo, CartRepository $cartRepository)
    {
        parent::__construct();
        $this->productRepository = $productRepo;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param Request $request
     * @param null $categoryId
     * @param null $orderType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request, $categoryId = null, $orderType = null)
    {
        $userId = !auth()->guest() ? auth()->user()->id : NULL;
        $workspaceId = $request->workspaceId;
        $locale = \App::getLocale();

        /** @var \App\Models\User $user */
        $user = \Auth::user();
        $token = null;
        // JWT token
        if ($user) {
            $token = \JWTAuth::fromUser($user);
        }

        if (empty($orderType)) {
            $orderType = $this->request->orderType;
        }
        
        $workspace = Workspace::find($workspaceId);
        $workspaceFull = $workspace->getFullInfo();
        $generalSet = $workspace->getSettingGeneral();
        $categories = $workspace->getListCategories();
        $timeslotDetails = $workspace->settingTimeslotDetails();

        $cart = $this->cartRepository->findWhere([
            'user_id'      => $userId,
            'workspace_id' => $workspaceId,
        ])->first();

        $groupProductIds = [];
        if ($cart && $cart->group_id) {
            $group = Group::whereId($cart->group_id)->first();
            if ($group) {
                $groupProducts = $group->getProducts();
                $groupProductIds = array_column($groupProducts, 'id');
            }
        }

        $categoriesLast = array();
        foreach ($categories as $category) {
            if ($cart && $cart->group_id) {
                $products = Product::where(['workspace_id' => $workspaceId, 'category_id' => $category['id'], 'active' => 1])
                ->whereIn('id', $groupProductIds)
                ->orderBy('order', 'asc')
                ->select('id')->get();

                if (count($products) > 0) {
                    if ($cart->type == Cart::TYPE_LEVERING) {
                        if ($category['available_delivery'] == 1) {
                            $categoriesLast[] = $category;
                        }
                    }
                    if ($cart->type == Cart::TYPE_TAKEOUT) {
                        $categoriesLast[] = $category;
                    }
                }
            }else {
                if($cart->type == Cart::TYPE_LEVERING) {
                    if($category['available_delivery'] == 1) {
                        $categoriesLast[] = $category;
                    }
                } else {
                    $categoriesLast[] = $category;
                }
            }
        }

        $categoryIds = collect($categoriesLast)->pluck('id')->all();

        $products = $this->productRepository->getProductFavourites($request, $workspaceId, auth()->user());
        foreach ($products as $product) {
            if(in_array($product->category_id, $categoryIds)) {
                $product = $product->getFullInfo();
                $productsLast[] = $product;
            }
        }

        return view($this->guard . '.user.index',
            [
                'categories' => $categoriesLast,
                'categoryId' => $categoryId,
                'products' => $productsLast ?? [],
                'orderType' => $orderType,
                'locale' => $locale,
                'workspaceId' => $workspaceId,
                'generalSet' => $generalSet,
                'workspace' => $workspaceFull,
                'timeslotDetailsSet' => $timeslotDetails->where('active', 1)->count() > 0,
                'cart' => $cart,
                'favourite' => 'favourite',
                'token'     => $token
            ]
        );
    }

    /**
     * @param Request $request
     * @param $workspaceId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id) {
        $user = User::find(auth()->user()->id);
        $product = $this->productRepository->findWithoutFail($id);
        $liked = $this->productRepository->toggleLike($user, $product);
        
        $result = array_merge($product->getFullInfo(), [
            'liked' => $liked,
        ]);

        return $this->sendResponse($result, trans('product.updated_successfully'));
    }
}