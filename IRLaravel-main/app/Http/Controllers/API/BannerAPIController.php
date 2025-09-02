<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBannerAPIRequest;
use App\Http\Requests\API\UpdateBannerAPIRequest;
use App\Models\Banner;
use App\Repositories\BannerRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class BannerController
 * @package App\Http\Controllers\API
 */
class BannerAPIController extends AppBaseController
{
    /** @var  BannerRepository */
    private $bannerRepository;

    public function __construct(BannerRepository $bannerRepo)
    {
        parent::__construct();

        $this->bannerRepository = $bannerRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->bannerRepository->pushCriteria(new RequestCriteria($request));
            $this->bannerRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $banners = $this->bannerRepository->paginate($limit);

        return $this->sendResponse($banners->toArray(), trans('banner.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateBannerAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateBannerAPIRequest $request)
    {
        $input = $request->all();

        $banners = $this->bannerRepository->create($input);

        return $this->sendResponse($banners->toArray(), trans('banner.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Banner $banner */
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            return $this->sendError(trans('banner.not_found'));
        }

        return $this->sendResponse($banner->toArray(), trans('banner.message_retrieved_successfully'));
    }

    /**
     * @param UpdateBannerAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBannerAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Banner $banner */
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            return $this->sendError(trans('banner.not_found'));
        }

        $banner = $this->bannerRepository->update($input, $id);

        return $this->sendResponse($banner->toArray(), trans('banner.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Banner $banner */
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            return $this->sendError(trans('banner.not_found'));
        }

        $banner->delete();

        return $this->sendResponse($id, trans('banner.message_deleted_successfully'));
    }
}
