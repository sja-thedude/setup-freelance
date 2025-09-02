<?php

namespace App\Http\Controllers\Frontend;

use App\Facades\Helper;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Group;
use App\Models\OptionItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Workspace;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\CartItemRepository;
use App\Repositories\CartOptionItemRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use Log;
use Webpatser\Uuid\Uuid;

class UserController extends BaseController
{
    /**
     * @var CartRepository
     */
    public $cartRepository;

    /**
     * @var CartItemRepository
     */
    public $cartItemRepository;

    /**
     * @var CartOptionItemRepository
     */
    public $cartOptionItemRepository;

    /**
     * @var
     */
    public $productRepository;

    /**
     * UserController constructor.
     * @param CartRepository $cartRepository
     * @param CartItemRepository $cartItemRepository
     * @param CartOptionItemRepository $cartOptionItemRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        CartOptionItemRepository $cartOptionItemRepository,
        ProductRepository $productRepository
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartOptionItemRepository = $cartOptionItemRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @param null $categoryId
     * @param null $orderType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $categoryId = null, $orderType = null)
    {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $userId = !auth()->guest() ? auth()->user()->id : NULL;
        $workspace = session('workspace_'.$workspaceSlug);
        $workspaceId = $workspace->id;
        $locale = \App::getLocale();
        $isTakeout = false;
        $isDelivery = false;
        $checkOrderTypes = \App\Models\SettingOpenHour::where(['workspace_id' => $workspaceId, 'active' => 1])->get();

        if(!$checkOrderTypes->isEmpty()) {
            foreach ($checkOrderTypes as $orderTypeItem) {
                if($orderTypeItem->type == 0) {
                    $isTakeout = true;
                }
                if($orderTypeItem->type == 1) {
                    $isDelivery = true;
                }
            }
        }

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

        $sessionCart = null;

        if (session('orderType')) {
            $inputs = session('orderType');
            $_userId = !empty($request->user_id) ? $request->user_id : (int) $inputs['user_id'];
            $_workspaceId = !empty($request->workspace_id) ? $request->workspace_id : (int) $inputs['workspace_id'];
            $sessionCart = $this->cartRepository->updateOrCreate([
                'user_id'      => $_userId,
                'workspace_id' => $_workspaceId,
            ], $inputs);
        }

        $cart = $cartCheck = $this->cartRepository->findWhere([
            'user_id'      => $userId,
            'workspace_id' => $workspaceId,
        ])
        ->first();

        if(empty($cartCheck) && !empty($sessionCart)) {
            $cartCheck = $sessionCart;
        }

        if($isDelivery == true &&
            $isTakeout == false &&
            (empty($cartCheck) || empty($cartCheck->lat) || empty($cartCheck->long) || empty($cartCheck->address))) {
            // check case group, don't need address
            if(session()->has('address_not_avaliable')) {
                session()->flash('address_not_avaliable', 1);
            }

            if(!(!empty($cartCheck) && !empty($cartCheck->group_id)) || session()->has('address_type')) {
                return redirect(route('web.index', ['address_unknown' => 1]));
            }
        }

        $isGroupInactive = false;
        $groupProductIds = [];

        if ($cart && $cart->group_id) {
            $group = Group::find($cart->group_id);

            if ($group && !$group->active) {
                $isGroupInactive = true;
            }

            if ($group) {
                $groupProducts = $group->getProducts();
                $groupProductIds = array_column($groupProducts, 'id');
            }
        }

        if (($request->get('tab') == Cart::TAB_TAKEOUT || ($cart && $cart->type == Cart::TYPE_TAKEOUT)) && session()->has('condition')) {
            session()->forget('condition');
        }

        // lấy danh sách category
        // Lấy danh sách category của nhà hàng
        $workspaceFull = $workspace->getFullInfo();
        $generalSet = $workspace->getSettingGeneral();
        $categories = $workspace->getListCategories();
        $timeslotDetails = $workspace->settingTimeslotDetails();

        // lấy danh sách sản phẩm theo từng category
        $productShow = array();
        $categoriesLast = array();
        $categoryIds = collect($categories)->pluck('id')->all();
        $tmpProducts = Product::with([
                'productFavorites',
                'workspace',
                'category',
                'vat',
                'allergenens',
                'productLabels',
                'translations',
                'productAvatar',
                'openTimeslots',
                'productLabelsActive'
            ])
            ->where('workspace_id', $workspaceId)
            ->whereIn('category_id', $categoryIds)
            ->where('active', 1)
            ->orderBy('order', 'asc');

        if (count($groupProductIds) > 0) {
            $tmpProducts = $tmpProducts->whereIn('id', $groupProductIds);
        }

        $tmpProducts = $tmpProducts->get();

        $categoryProducts = [];

        if(!empty($categoryIds)) {
            foreach($categoryIds as $tmpCategoryId) {
                if(!$tmpProducts->isEmpty()) {
                    foreach($tmpProducts as $product) {
                        if($product->category_id == $tmpCategoryId) {
                            if(!empty($categoryProducts[$tmpCategoryId])) {
                                array_push($categoryProducts[$tmpCategoryId], $product);
                            } else {
                                $categoryProducts[$tmpCategoryId] = [$product];
                            }
                        }
                    }
                }
            }
        }

        foreach ($categories as $category) {
            // Lay tat ca san pham thuoc category
            $products = !empty($categoryProducts[$category['id']]) ? $categoryProducts[$category['id']] : [];
            $productsLast = array();

            foreach ($products as $product) {
                $product = $product->getFullInfo();
                $productsLast[] = $product;
            }

            $category['products'] = $productsLast;

            if ($cart && $cart->group_id) {
                if (count($category['products']) > 0) {
                    if($cart->type == Cart::TYPE_LEVERING) {
                        if($category['available_delivery'] == 1) {
                            $categoriesLast[] = $category;
                        }
                    }
                    if($cart->type == Cart::TYPE_TAKEOUT) {
                        $categoriesLast[] = $category;
                    }
                }
            } else {
                if($request->get('tab') == Cart::TAB_LEVERING) {
                    if($category['available_delivery'] == 1) {
                        $categoriesLast[] = $category;
                    }
                } else {
                    $categoriesLast[] = $category;
                }
            }
        }

        if(is_null($categoryId)) {
            $productShow = !empty($categoriesLast[0]) ? $categoriesLast[0]['products'] : [];
        } else {
            foreach ($categoriesLast as $category) {
                if($category['id'] == $categoryId) {
                    $productShow = $category['products'];
                    $currentCategory = $category;
                }
            }
        }

        $this->checkCartCoupon($cart, $workspaceId);
        $currentCategory = !empty($currentCategory) ? $currentCategory : [];

        return view($this->guard . '.user.index',
            [
                'categories'         => $categoriesLast,
                'categoryId'         => $categoryId,
                'currentCategory'    => $currentCategory,
                'products'           => $productShow,
                'orderType'          => $orderType,
                'locale'             => $locale,
                'workspaceId'        => $workspaceId,
                'generalSet'         => $generalSet,
                'timeslotDetailsSet' => $timeslotDetails->where('active', 1)->count() > 0,
                'workspace'          => $workspaceFull,
                'cart'               => $cart,
                'isGroupInactive'    => $isGroupInactive,
                'token'             => $token
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchProduct(Request $request) {
        $userId = !auth()->guest() ? auth()->user()->id : NULL;
        $workspaceId = $request->workspaceId;
        $data = $request->post();

        // Lay tat ca san pham thuoc category
        $products = Product::select('products.*')
            ->from('products')
            ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
            ->where([[
                'product_translations.name', 'like', '%' . $data['q'] . '%'],
                'products.active' => 1,
                'product_translations.locale' => $data['locale'],
                'products.workspace_id' => $data['workspace_id'
            ]]);

        if($data['order'] == 1) {
            $products->orderBy('price', 'asc');
        } elseif ($data['order'] == 2) {
            $products->orderBy('price', 'desc');
        } elseif ($data['order'] == 3) {
            $products->orderBy('order', 'asc');
        }

        $productsA = $products->get();
        $productsLast = array();
        foreach ($productsA as $product) {
            $product = $product->getFullInfo();
            $productsLast[] = $product;
        }

        if($data['order'] == 3) {
            $productsLast = array_sort($productsLast, 'name', SORT_ASC); // Sort by surname
        }

        $workspace = Workspace::find($data['workspace_id']);
        $generalSet = $workspace->getSettingGeneral();

        $cart = $this->cartRepository->findWhere([
            'user_id'      => $userId,
            'workspace_id' => $workspaceId,
        ])->first();

        $timeslotDetails = $workspace->settingTimeslotDetails();
        $currentCategory = Category::find($data['category_id']);

        return view($this->guard . '.user.product',
            [
                'products' => $productsLast,
                'generalSet' => $generalSet,
                'locale' => $data['locale'],
                'currentCategory' => $currentCategory,
                'workspaceId' => $data['workspace_id'],
                'categoryId' => $data['category_id'],
                'orderType' => $data['order'],
                'timeslotDetailsSet' => $timeslotDetails->where('active', 1)->count() > 0,
                'cart' => $cart,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail(Request $request) {
        $data = $request->post();
        $product = Product::find($data['id']);
        $productFull = $product->getFullInfo();
        $productOptions = \App\Models\ProductOption::with('option')
            ->where('product_id', $data['id'])
            ->where('is_checked', 1)
            ->get();

        $optionsFull = array();
        foreach ($productOptions as $productOption) {
            $option = $productOption->option ? $productOption->option->getFullInfo() : null;
            $items = OptionItem::where([
                'opties_id' => $option['id'],
                'available' => 1
            ])
            ->orderBy('order')
            ->get();

            $option['items'] = $items;
            $optionsFull[] = $option;
        }

        return view($this->guard . '.partials.product-detail',
            [
                'locale' => $data['locale'],
                'product' => $productFull,
                'options' => $optionsFull,
                'workspace' => $product->workspace,
                'category' => $product->category,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProduct(Request $request) {
        $userId = !auth()->guest() ? auth()->user()->id : NULL;
        $workspaceId = $request->workspaceId;
        $data = $request->post();
        if (empty($data['order'])) {
            $data['order'] = 1;
        }

        // Lay tat ca san pham thuoc category
        $products = Product::select('products.*')
                ->from('products')
                ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
                ->where('workspace_id', $workspaceId);

        if(!empty($data['is_search']) || !empty($data['q'])) {
            $products = $products->whereHas('categories', function($q){
                    $q->whereNull("deleted_at");
                })
                ->where([['product_translations.name', 'like', '%' . $data['q'] . '%'], 'products.active' => 1, 'product_translations.locale' => $data['locale'], 'products.workspace_id' => $data['workspace_id']]);
        } else if ($data['category_id']) {
            $products = $products->where(['category_id' => $data['category_id'], 'active' => 1]);
        }

        $favourite = null;
        if ($request->has('favourite')) {
            $products = $products->join('product_favorites', 'product_favorites.product_id', '=', 'products.id')
                    ->where('product_favorites.user_id', $userId)
                    ->groupBy('products.id')
                    ->orderBy('order', 'desc');

            $favourite = 'favourite';
        }

        if($data['order'] == 1) {
            $products->orderBy('price', 'asc');
        } elseif ($data['order'] == 2) {
            $products->orderBy('price', 'desc');
        } else {
            $products->orderBy('order', 'asc');
        }

        $productsA = $products->groupBy('products.id')->get();
        $productsLast = array();
        foreach ($productsA as $product) {
            $product = $product->getFullInfo();
            $productsLast[] = $product;
        }

        if($data['order'] == 3) {
            $productsLast = array_sort($productsLast, 'name', SORT_ASC); // Sort by surname
        }

        $productsLastInit = $productsLast;

        $workspace = Workspace::find($data['workspace_id']);
        $generalSet = $workspace->getSettingGeneral();

        $cart = $this->cartRepository->findWhere([
            'user_id'      => $userId,
            'workspace_id' => $workspaceId,
        ])->first();

        $timeslotDetails = $workspace->settingTimeslotDetails();
        $currentCategory = Category::find($data['category_id']);

        $noProductFound = null;
        if($request->ajax()){
            $noProductFound = trans('frontend.no_product_found');
        }

        // Get categories with product using mobile view
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug);
        $categories = $workspace->getListCategories();
        $categoriesLast = array();

        $productsLast = array();
        foreach ($categories as $category) {
            // Lay tat ca san pham thuoc category
            $products = Product::select('products.*')
                ->from('products')->with([
                    'productFavorites',
                    'workspace',
                    'category',
                    'vat',
                    'allergenens',
                    'productLabels',
                    'translations',
                    'productAvatar',
                    'openTimeslots',
                ])
                ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
                ->where('workspace_id', $workspaceId);

            if(!empty($data['is_search']) || !empty($data['q'])) {
                $products = $products->where(['category_id' => $category['id'], 'active' => 1])
                    ->where([['product_translations.name', 'like', '%' . $data['q'] . '%'], 'products.active' => 1, 'product_translations.locale' => $data['locale'], 'products.workspace_id' => $data['workspace_id']]);
            } else if ($data['category_id']) {
                $products = $products->where(['category_id' => $category['id'], 'active' => 1, 'product_translations.locale' => $data['locale'], 'products.workspace_id' => $data['workspace_id']]);
            }

            $products->orderBy('order', 'asc');

            if ($request->has('favourite')) {
                $products = $products->join('product_favorites', 'product_favorites.product_id', '=', 'products.id')
                        ->where('product_favorites.user_id', $userId)
                        ->groupBy('products.id');
                $products->orderBy('order', 'desc');
            }

            $products = $products->get();
            $productsLast = array();
            foreach ($products as $product) {
                $product = $product->getFullInfo();
                $productsLast[] = $product;
            }

            $category['products'] = $productsLast;

            if(!empty($data['is_search']) || !empty($data['q'])) {
                if(!empty($category['products'])) {
                    $categoriesLast[] = $category;
                }
            } else {
                $categoriesLast[] = $category;
            }
        }
        // end

        $productsLast = $productsLastInit;

        return view($this->guard . '.user.product',
            [
                'categories' => $categoriesLast,
                'products' => $productsLast,
                'generalSet' => $generalSet,
                'locale' => $data['locale'],
                'currentCategory' => $currentCategory,
                'workspaceId' => $data['workspace_id'],
                'categoryId' => $data['category_id'],
                'orderType' => $data['order'],
                'favourite' => $favourite,
                'timeslotDetailsSet' => $timeslotDetails->where('active', 1)->count() > 0,
                'cart' => $cart,
                'noProductFound' => $noProductFound
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCart(Request $request) {
        try {
            \DB::beginTransaction();

            $cartOptItem = array();
            $optionCheck = array();
            $cartOptionItems = $request->cartOptionItem ?: array();
            $product = $this->productRepository->with('productOptions')->findWhere(['id' => $request->product_id, 'active' => 1])->first();

            if (empty($product)) {
                Flash::error(trans('frontend.message_for_product_not_available'));
                return $this->sendResponse([], trans('Successfully'));
            }

            $productOptions = $product->productOptions()->where('is_checked', 1)->get();

            if (!empty($product) && !$productOptions->isEmpty()) {
                foreach ($productOptions as $item) {
                    $option = $item->option;
                    if (!empty($option)) {
                        $optionCheck[$item->opties_id] =  [
                            'min' => $option->min,
                            'max' => $option->max,
                            'count' => 0
                        ];
                    }
                }
            }

            //Store carts
            $cart = $request->only([
                'workspace_id',
                'user_id',
                'group_id',
                'coupon_id',
                'type',
                'address_type',
                'address',
                'note',
            ]);

            //Store cart when user login
            if ($request->user_id) {
                $cart = $this->cartRepository->updateOrCreate([
                    'user_id' => $request->user_id,
                    'workspace_id' => $request->workspace_id
                ],$cart);
            } else {
                $cart['id'] = 1;
            }

            //Store cart_item
            $cartItem = $request->only([
                'workspace_id',
                'category_id',
                'product_id',
                'type',
                'total_number'
            ]);

            if ($request->user_id) {
                $cartItem['cart_id'] = $cart->id;
            }

            //Step 1: Check if the product is identical and the optitems are the same
            if ($request->user_id) {
                $identicalCartItem = $this->cartOptionItemRepository->getIdenticalCartItem($cartItem, $cartOptionItems);
            }

            //Store cart when user login
            if ($request->user_id) {
                if (!$identicalCartItem) {
                    $newCartItem = $this->cartItemRepository->create($cartItem);
                } else {
                    $newCartItem = $identicalCartItem;
                }
            } else {
                //Fake id
                $randomId = md5(microtime());
                $newCartItem[$randomId] =  $cartItem;
                $newCartItem[$randomId]['id'] =  $randomId;
                $newCartItem[$randomId]['cart_id'] =  $cart['id'];
            }

            //Store cart_option_items
            $arrayOptions = [];
            if (!empty($cartOptionItems)) {
                $newOtp = [];

                foreach ($cartOptionItems as $key => $item) {
                    $item = \GuzzleHttp\json_decode($item, true);
                    if(!empty($newOtp[$item['opties_id']])) {
                        $newOtp[$item['opties_id']] = $newOtp[$item['opties_id']] + 1;
                    } else {
                        $newOtp[$item['opties_id']] = 1;
                    }

                    $optionCheck[$item['opties_id']]['count'] = $newOtp[$item['opties_id']];

                    if ($request->user_id) {
                        $cartOptItem[$key]['cart_item_id'] = $newCartItem->id;
                    } else {
                        //Fake id
                        $cartOptItem[$key]['cart_item_id'] = $newCartItem[$randomId]['id'];
                    }

                    $cartOptItem[$key]['product_id'] = (int)$request->product_id;
                    $cartOptItem[$key]['optie_id'] = $item['opties_id'];
                    $cartOptItem[$key]['optie_item_id'] = $item['id'];
                    $cartOptItem[$key]['workspace_id'] = $request->workspace_id;
                    $cartOptItem[$key]['created_at'] = Carbon::now();
                    $cartOptItem[$key]['updated_at'] = Carbon::now();
                    unset($item);
                }
            }

            //Valid option before book
            $check = true;
            if (!empty($optionCheck)) {
                foreach ($optionCheck as $key => $value) {
                    if ($value['count'] < $value['min']) {
                        $check = false;
                        $arrayOptions[$key] = [
                            'id' => $key,
                            'msg' => trans('frontend.valid_select_min', ['count' => $value['min']])
                        ];
                    } elseif ($value['count'] > $value['max']) {
                        $check = false;
                        $arrayOptions[$key] = [
                            'id' => $key,
                            'msg' => trans('frontend.valid_select_max', ['count' => $value['max']])
                        ];
                    }
                }
            }

            //Check and insert
            if ($check) {
                //Store cart when user login
                //Step 2: Update the total number if identical
                if (!empty($identicalCartItem)) {
                    $newTotalNumber = $cartItem['total_number'] + $identicalCartItem->total_number;
                    $this->cartItemRepository->update(['total_number' => $newTotalNumber], $identicalCartItem->id);
                }

                if ($request->user_id) {
                    if (!$identicalCartItem) {
                        $this->cartOptionItemRepository->saveMany($cartOptItem);
                    }
                } else {
                    foreach ($newCartItem as $t => $cartItem) {
                        $newCartItem[$t]['cart_option_items'] = $cartOptItem;
                    }

                    $newAddCartItem = array();

                    if (session()->has('cart_without_login_'.$this->workspaceSlug)) {
                        $cart = session()->get('cart_without_login_'.$this->workspaceSlug);
                        $identicalCartItemWithoutLogin = \App\Helpers\Helper::getIdenticalCartItemWithoutLogin($cart, $newCartItem, $randomId);
                        $newAddCartItem = \App\Helpers\Helper::handleCartWithoutLogin($cart);
                    }

                    if (!empty($identicalCartItemWithoutLogin)) {
                        $this->cartItemRepository->handleIfIdentical(
                            $newCartItem,
                            $identicalCartItemWithoutLogin,
                            $newAddCartItem,
                            $randomId
                        );
                    }

                    $newCartItem = array_merge($newCartItem, $newAddCartItem);

                    unset($cart['id']);
                    $cart['user_id'] = Uuid::generate()->string; // Fake id for user
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

                    session(['cart_without_login_'.$this->workspaceSlug => $this->cartRepository->with([
                        'cartItems',
                        'coupon',
                        'workspace.workspaceExtras',
                        'workspace.settingDeliveryConditions',
                        'workspace.settingPreference',
                        'workspace.settingOpenHours',
                        'workspace.settingPayments',
                        'workspace.settingTimeslots',
                        'workspace.settingExceptHoursExtend',
                        'cartItems.product.category',
                        'cartItems.product.vat',
                        'cartItems.cartOptionItems',
                        'cartItems.cartOptionItems.option',
                        'cartItems.cartOptionItems.optionItem',
                        'cartItems.category.productSuggestions.product',
                    ])->find($objCart->id)]);

                    $this->cartRepository->delete($objCart->id);

                    return $this->sendResponse([
                        'cart' => json_encode($cart),
                        'cartItem' => json_encode($newCartItem),
                        'cartOptItem' => json_encode($cartOptItem)
                    ], trans('Successfully'));
                }
            } else {
                return $this->sendError("Error", 400, $arrayOptions);
            }

            \DB::commit();

            return $this->sendResponse([], trans('Successfully'));
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
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function error(Request $request)
    {
        $withData = $request->all();
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $sessionWorkspaceSlug = 'workspace_' . ($workspaceSlug ?? '');

        if (empty(session($sessionWorkspaceSlug))) {
            $workspace = null;

            if ($request->has('workspaceId')) {
                $workspace = Workspace::where('id', $request->get('workspaceId'))
                    ->where('active', true)
                    ->first();
            } else if ($request->has('order_id')) {
                $order = Order::find($request->get('order_id'));

                if (!empty($order) && !empty($order->workspace)) {
                    $workspace = $order->workspace;
                }
            }

            session([$sessionWorkspaceSlug => $workspace]);
        }

        $workspace = session($sessionWorkspaceSlug)->refresh();
        $workspaceFull = $workspace->getFullInfo();

        // Config for deeplink
        $config = Helper::getMobileConfig($request);

        return view($this->guard . '.user.error', [
            'config' => $config,
            'workspace' => $workspaceFull
        ])->with($withData);
    }

    /**
     * @param $array
     * @param $on
     * @param int $order
     * @return array
     */
    function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    /**
     * Check conditions for coupon in carts after logging in
     *
     * @param $cart
     * @param $workspaceId
     * @throws \Exception
     */
    protected function checkCartCoupon($cart, $workspaceId)
    {
        try {
            if ($cart && $cart->coupon) {
                $productIds = $cart->cartItems->pluck('product_id')->toArray();
                $code = $cart->coupon->code;
                $userId = !auth()->guest() ? auth()->user()->id : NULL;

                $this->productRepository->validateProductCoupon($productIds, $code, $userId, $workspaceId);
            }
        } catch (\Exception $exception) {
            $code = $cart->coupon->code;
            $cart->coupon_id = NULL;
            $cart->save();
            session()->flash('cart_coupon_code', $code);
            session()->flash('cart_coupon_error', $exception->getMessage());
        }
    }
}
