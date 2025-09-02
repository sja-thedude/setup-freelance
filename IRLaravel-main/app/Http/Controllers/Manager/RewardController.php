<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\Group;
use App\Models\Reward;
use App\Repositories\CategoryRepository;
use App\Repositories\RewardCategoryRepository;
use App\Repositories\RewardProductRepository;
use App\Repositories\RewardRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingGeneralRepository;
use App\Repositories\WorkspaceExtraRepository;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use App\Repositories\CategoryRelationRepository;
use Log;

/**
 * Class RewardController
 *
 * @package App\Http\Controllers\Manager
 */
class RewardController extends BaseController
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
     * @var RewardProductRepository
     */
    private $rewardProductRepository;

    /**
     * @var RewardCategoryRepository
     */
    private $rewardCategoryRepository;

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
     * @var RewardRepository
     */
    private $rewardRepository;

    /**
     * @var WorkspaceExtraRepository
     */
    private $workspaceExtraRepository;

    /**
     * @var SettingGeneralRepository
     */
    private $settingGeneralRepository;

    private $categoryRelationRepository;

    /**
     * RewardController constructor.
     * @param CategoryRepository $categoryRepository
     * @param OptionRepository $optionRepository
     * @param RewardProductRepository $rewardProductRepository
     * @param RewardCategoryRepository $rewardCategoryRepository
     * @param ProductAllergenenRepository $productAllergenenRepository
     * @param OpenTimeslotRepository $openTimeslotRepository
     * @param MediaRepository $mediaRepository
     * @param ProductRepository $productRepository
     * @param RewardRepository $rewardRepository
     * @param WorkspaceExtraRepository $workspaceExtraRepository
     * @param SettingGeneralRepository $settingGeneralRepository
     * @param CategoryRelationRepository $categoryRelationRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        RewardProductRepository $rewardProductRepository,
        RewardCategoryRepository $rewardCategoryRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        RewardRepository $rewardRepository,
        WorkspaceExtraRepository $workspaceExtraRepository,
        SettingGeneralRepository $settingGeneralRepository,
        CategoryRelationRepository $categoryRelationRepository
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->rewardProductRepository = $rewardProductRepository;
        $this->rewardCategoryRepository = $rewardCategoryRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->rewardRepository = $rewardRepository;
        $this->workspaceExtraRepository = $workspaceExtraRepository;
        $this->settingGeneralRepository = $settingGeneralRepository;
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
        $request->request->add(['workspace_id' => $this->tmpUser->workspace_id]);

        $rewards = $this->rewardRepository->paginate(20);

        $setting = $this->settingGeneralRepository
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id])
            ->first();

        return view('manager.rewards.index', compact('rewards', 'setting'));
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
            'data' => view('manager.rewards.partials.form', [
                'products'                => $products,
                'productsGroupByCategory' => $productsGroupByCategory,
                'action'                  => route($this->guard . '.rewards.store'),
                'method'                  => 'POST',
                'idForm'                  => 'create_reward',
                'titleModal'              => trans('reward.nieuwe_ategorie'),
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRewardRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRewardRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id']    = $this->tmpUser->workspace_id;
            $data['expire_date']     = Helper::convertDateTimeToUTC($data['expire_date'], $request->timeZone)->format('Y-m-d H:i:s');
            $data['files']['file'][] = $request->uploadAvatar ?? "undefined";

            if ($data['type'] == Reward::FYSIEK_CADEAU) {
                $data['reward'] = 0;
            }

            $reward = $this->rewardRepository->create($data);

            if ($data['type'] == Reward::KORTING) {
                $rewardProducts = array();
                $listChoosenProduct = array_filter(explode(',', $data['listProduct']));
                foreach ($listChoosenProduct as $k => $id) {
                    $rewardProducts[$k]['reward_id']  = $reward->id;
                    $rewardProducts[$k]['product_id'] = (int) $id;
                }

                $this->rewardProductRepository->saveMany($rewardProducts);

                //Save categories
                if (!empty($request->category_ids)) {
                    $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Reward::class, $reward->id);
                }
            }

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('reward.created_successfully'));
            }

            Flash::success(trans('reward.created_successfully'));

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
     * @param int     $rewardId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $rewardId)
    {
        $reward = $this->rewardRepository->with(['categories', 'products'])->findWithoutFail($rewardId);

        if (empty($reward)) {
            Flash::error(trans('reward.not_found'));
            return redirect(route($this->guard . '.rewards.index'));
        }

        $products = $this->productRepository->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $media = $this->mediaRepository->findWhere([
            'foreign_id'    => $rewardId,
            'foreign_model' => Reward::class
        ])->first();

        $productsGroup = $this->categoryRepository
            ->with('products')
            ->orderBy('order')
            ->findWhere(['workspace_id' => $this->tmpUser->workspace_id]);

        $productsGroupByCategory = Helper::handleDataSelect2($productsGroup);

        $categories = $productsGroup->pluck('name', 'id')->toArray();
        $categoryIds = $reward->categoriesRelation()->pluck('category_id')->toArray();

        return response()->json([
            'code' => 200,
            'data' => view('manager.rewards.partials.form', [
                'productsGroupByCategory' => $productsGroupByCategory,
                'products'                => $products,
                'reward'                  => $reward,
                'media'                   => $media,
                'action'                  => route($this->guard . '.rewards.update', $reward->id),
                'method'                  => 'PUT',
                'idForm'                  => 'update_reward',
                'titleModal'              => trans('reward.categorie_wijzigen'),
                'categories' => $categories,
                'categoryIds' => $categoryIds
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRewardRequest $request
     * @param int                  $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRewardRequest $request, int $rewardId)
    {
        $reward = $this->rewardRepository->findWithoutFail($rewardId);

        if (empty($reward)) {
            Flash::error(trans('reward.not_found'));
            return redirect(route($this->guard . '.rewards.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();
            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['expire_date']  = Carbon::parse($data['expire_date'])->format('Y-m-d H:i:s');

            if (isset($request->uploadAvatar)) {
                $data['files']['file'][] = $request->uploadAvatar;
            }

            if ($data['type'] == Reward::FYSIEK_CADEAU) {
                $data['reward'] = 0;
            }

            $reward = $this->rewardRepository->update($data, $rewardId);

            $this->rewardProductRepository->deleteWhere([
                'reward_id' => $rewardId
            ]);

            if ($data['type'] == Reward::KORTING) {
                $rewardProducts = array();
                $listChoosenProduct = array_filter(explode(',', $data['listProduct']));

                foreach ($listChoosenProduct as $k => $id) {
                    $rewardProducts[$k]['reward_id']  = $reward->id;
                    $rewardProducts[$k]['product_id'] = (int) $id;
                }

                $this->rewardProductRepository->saveMany($rewardProducts);

                //Save categories
                $this->categoryRelationRepository->deleteWhere(['foreign_model' => Reward::class, 'foreign_id' => $reward->id]);
                if (!empty($request->category_ids)) {
                    $this->categoryRelationRepository->updateOrCreateCategories($request->category_ids, Reward::class, $reward->id);
                }
            }

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('reward.updated_successfully'));
            }

            Flash::success(trans('reward.updated_successfully'));

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
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $reward = $this->rewardRepository->findWithoutFail($id);

        if (empty($reward)) {
            Flash::error(trans('reward.not_found'));
            return redirect(route($this->guard . '.rewards.index'));
        }

        $this->rewardRepository->delete($id);

        $this->categoryRelationRepository->deleteWhere(['foreign_model' => Reward::class, 'foreign_id' => $reward->id]);

        return response()->json([
            'success'  => 'success',
            'message' => trans('reward.deleted_confirm'),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function settingInstellingen(Request $request)
    {
        $this->settingGeneralRepository->updateOrCreate([
            'workspace_id' => $this->tmpUser->workspace_id
        ], [
            'instellingen' => $request->instellingen
        ]);

        return redirect()->back()->withInput();
    }
}
