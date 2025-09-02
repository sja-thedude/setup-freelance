<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkspaceAPIRequest;
use App\Http\Requests\API\UpdateWorkspaceAPIRequest;
use App\Models\Workspace;
use App\Repositories\RestaurantCategoryRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WorkspaceController
 * @package App\Http\Controllers\API
 */
class WorkspaceAPIController extends AppBaseController
{
    /**
     * @var WorkspaceRepository $workspaceRepository
     */
    protected $workspaceRepository;

    /**
     * @var RestaurantCategoryRepository $restaurantCategoryRepository
     */
    protected $restaurantCategoryRepository;

    /**
     * WorkspaceAPIController constructor.
     * @param WorkspaceRepository $workspaceRepo
     * @param RestaurantCategoryRepository $restaurantCategoryRepo
     */
    public function __construct(
        WorkspaceRepository $workspaceRepo,
        RestaurantCategoryRepository $restaurantCategoryRepo
    )
    {
        parent::__construct();

        $this->workspaceRepository = $workspaceRepo;
        $this->restaurantCategoryRepository = $restaurantCategoryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->merge([
            // Only show active restaurant
            'active' => true,
            /*// Only show online restaurant
            'is_online' => true,*/
            'display_in_app' => true,
        ]);

        $this->workspaceRepository->pushCriteria(new RequestCriteria($request));
        $this->workspaceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $limit = $this->workspaceRepository->getPagingLimit($request);
        $workspaces = $this->workspaceRepository
            ->with(['workspaceAvatar', 'workspaceCategories', 'workspaceExtras', 'workspaceGalleries', 'settingGeneral'])
            ->paginate($limit);
        $workspaceIds = $workspaces->pluck('id')->toArray();
        $favoriet_friet = $this->workspaceRepository->checkCategoryFavorite('favoriet_friet', $workspaceIds);
        $kokette_kroket = $this->workspaceRepository->checkCategoryFavorite('kokette_kroket', $workspaceIds);
        $timezone = $request->header('Timezone', config('app.timezone'));
        $openHours = $this->workspaceRepository->checkOpenHours($workspaceIds, $request->get('open_type'), $timezone);

        $workspaces->transform(function ($workspace) use ($favoriet_friet, $kokette_kroket, $openHours) {
            /** @var \App\Models\Workspace $workspace */
            // Get full information
            $data = $workspace->getFullInfo();
            // Merge with custom data
            $data = array_merge($data, [
                'favoriet_friet' => array_get($favoriet_friet, $workspace->id, false),
                'kokette_kroket' => array_get($kokette_kroket, $workspace->id, false),
                'is_open' => array_get($openHours, $workspace->id, false),
                'price' => $workspace->price,
                'price_min' => $workspace->price_min,
            ]);

            return $data;
        });
        $result = $workspaces->toArray();

        return $this->sendResponse($result, trans('workspace.message_retrieved_list_successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ordered(Request $request)
    {
        $request->merge([
            // Only show active restaurant
            'active' => true,
            /*// Only show online restaurant
            'is_online' => true,*/
            'display_in_app' => true,
        ]);

        $this->workspaceRepository->pushCriteria(new RequestCriteria($request));
        $this->workspaceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $limit = $this->workspaceRepository->getPagingLimit($request);
        $workspaces = $this->workspaceRepository->ordered($limit);
        $user = $request->user();
        $workspaceIds = $workspaces->pluck('id')->toArray();
        $latestOrdered = $this->workspaceRepository->latestOrdered($user, $workspaceIds);
        $favoriet_friet = $this->workspaceRepository->checkCategoryFavorite('favoriet_friet', $workspaceIds);
        $kokette_kroket = $this->workspaceRepository->checkCategoryFavorite('kokette_kroket', $workspaceIds);
        $timezone = $request->header('Timezone', config('app.timezone'));
        $openHours = $this->workspaceRepository->checkOpenHours($workspaceIds, $request->get('open_type'), $timezone);

        $workspaces->transform(function ($workspace) use ($latestOrdered, $favoriet_friet, $kokette_kroket, $openHours) {
            /** @var \App\Models\Workspace $workspace */
            // Get full information
            $data = $workspace->getFullInfo();
            // Merge with custom data
            $data = array_merge($data, [
                'latest_order' => array_get($latestOrdered, $workspace->id),
                'favoriet_friet' => array_get($favoriet_friet, $workspace->id, false),
                'kokette_kroket' => array_get($kokette_kroket, $workspace->id, false),
                'is_open' => array_get($openHours, $workspace->id, false),
            ]);

            return $data;
        });
        $result = $workspaces->toArray();

        return $this->sendResponse($result, trans('workspace.message_retrieved_list_successfully'));
    }

    public function languages(Request $request, $id)
    {
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            return $this->sendError(trans('workspace.not_found'));
        }

        return $this->sendResponse([
            'active_languages' => $workspace->active_languages
        ], '');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function liked(Request $request)
    {
        $request->merge([
            // Only show active restaurant
            'active' => true,
            /*// Only show online restaurant
            'is_online' => true,*/
            'display_in_app' => true,
        ]);

        $this->workspaceRepository->pushCriteria(new RequestCriteria($request));
        $this->workspaceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $limit = $this->workspaceRepository->getPagingLimit($request);
        $workspaces = $this->workspaceRepository->liked($limit);
        $user = $request->user();
        $workspaceIds = $workspaces->pluck('id')->toArray();
        $favoriteProducts = $this->workspaceRepository->countFavoriteProducts($user, $workspaceIds);
        $favoriet_friet = $this->workspaceRepository->checkCategoryFavorite('favoriet_friet', $workspaceIds);
        $kokette_kroket = $this->workspaceRepository->checkCategoryFavorite('kokette_kroket', $workspaceIds);
        $timezone = $request->header('Timezone', config('app.timezone'));
        $openHours = $this->workspaceRepository->checkOpenHours($workspaceIds, $request->get('open_type'), $timezone);

        $workspaces->transform(function ($workspace) use ($favoriteProducts, $favoriet_friet, $kokette_kroket, $openHours) {
            /** @var \App\Models\Workspace $workspace */
            // Get full information
            $data = $workspace->getFullInfo();
            // Merge with custom data
            $data = array_merge($data, [
                'total_favorite' => array_get($favoriteProducts, $workspace->id, 0),
                'favoriet_friet' => array_get($favoriet_friet, $workspace->id, false),
                'kokette_kroket' => array_get($kokette_kroket, $workspace->id, false),
                'is_open' => array_get($openHours, $workspace->id, false),
            ]);

            return $data;
        });
        $result = $workspaces->toArray();

        return $this->sendResponse($result, trans('workspace.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateWorkspaceAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateWorkspaceAPIRequest $request)
    {
        $input = $request->all();

        $workspace = $this->workspaceRepository->create($input);

        return $this->sendResponse($workspace->toArray(), trans('workspace.message_created_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            return $this->sendError(trans('workspace.not_found'));
        }

        $result = $workspace->getFullInfo();

        return $this->sendResponse($result, trans('workspace.message_retrieved_successfully'));
    }

    /**
     * @param UpdateWorkspaceAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorkspaceAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            return $this->sendError(trans('workspace.not_found'));
        }

        $workspace = $this->workspaceRepository->update($input, $id);

        return $this->sendResponse($workspace->toArray(), trans('workspace.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findWithoutFail($id);

        if (empty($workspace)) {
            return $this->sendError(trans('workspace.not_found'));
        }

        $workspace->delete();

        return $this->sendResponse($id, trans('workspace.message_deleted_successfully'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        $this->restaurantCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->restaurantCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $limit = $this->restaurantCategoryRepository->getPagingLimit($request);
        $restaurantCategories = $this->restaurantCategoryRepository->paginate($limit);

        $restaurantCategories->transform(function ($item) {
            /** @var \App\Models\RestaurantCategory $item */
            return $item->getFullInfo();
        });
        $result = $restaurantCategories->toArray();

        return $this->sendResponse($result, trans('restaurant_category.message_retrieved_list_successfully'));
    }

    /**
     * Get Workspace by token
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByToken(string $token)
    {
        try {
            $workspace = $this->workspaceRepository->getByToken($token);

            $result = $workspace->getFullInfo();

            return $this->sendResponse($result, trans('workspace.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    /**
     * Get App settings of Workspace by token
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppSettings(string $token)
    {
        try {
            $settings = $this->workspaceRepository->getAppSettings($token);

            return $this->sendResponse($settings, trans('workspace.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    /**
     * Get App settings of workspace by id
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppSettingsById($id)
    {
        try {
            $settings = $this->workspaceRepository->getAppSettingsById($id);

            return $this->sendResponse($settings, trans('workspace.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    public function getByDomain(string $domain)
    {
        try {
            $workspace = $this->workspaceRepository->getByDomain($domain);

            $result = $workspace->getFullInfo();
            $result['token'] = $workspace->token;

            return $this->sendResponse($result, trans('workspace.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    public function validateOrderAccessKey(Request $request, $id) {
        try {
            $valid = $this->workspaceRepository->validateOrderAccessKey($id, $request->get('access_key', null));

            return $this->sendResponse(compact('valid'), trans('workspace.message_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }
}
