<?php

namespace App\Http\Controllers\Backend;

use App\Models\Banner;
use App\Http\Requests\CreateBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Repositories\BannerRepository;
use Helper;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BannerController
 * @package App\Http\Controllers\Backend
 */
class BannerController extends BaseController
{
    /** @var  BannerRepository */
    private $bannerRepository;

    /**
     * BannerController constructor.
     * @param BannerRepository $bannerRepo
     */
    public function __construct(BannerRepository $bannerRepo)
    {
        parent::__construct();

        $this->bannerRepository = $bannerRepo;

        // Set Dir for KCFinder
        $uploadDir = config('common.banner_directory');
        Helper::setKCFinderUploadDir($uploadDir);
    }

    /**
     * Display a listing of the Banner.
     *
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        // Order by order column ascending
        $request->merge([
            'orderBy' => 'order',
            'sortedBy' => 'asc',
        ]);
        $this->bannerRepository->pushCriteria(new RequestCriteria($request));
        $banners = $this->bannerRepository->paginate();

        return view('admin.banners.index')
            ->with('banners', $banners);
    }

    /**
     * Show the form for creating a new Banner.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created Banner in storage.
     *
     * @param CreateBannerRequest $request
     *
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateBannerRequest $request)
    {
        $input = $request->all();

        $banner = $this->bannerRepository->create($input);

        Flash::success(trans('messages.banner.created_successfully'));

        return redirect(route('admin.banners.index'));
    }

    /**
     * Display the specified Banner.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            Flash::error(trans('messages.banner.not_found'));

            return redirect(route('admin.banners.index'));
        }

        return view('admin.banners.show')->with('banner', $banner);
    }

    /**
     * Show the form for editing the specified Banner.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            Flash::error(trans('messages.banner.not_found'));

            return redirect(route('admin.banners.index'));
        }

        return view('admin.banners.edit')->with('banner', $banner);
    }

    /**
     * Update the specified Banner in storage.
     *
     * @param  int $id
     * @param UpdateBannerRequest $request
     *
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateBannerRequest $request)
    {
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            Flash::error(trans('messages.banner.not_found'));

            return redirect(route('admin.banners.index'));
        }

        $banner = $this->bannerRepository->update($request->all(), $id);

        Flash::success(trans('messages.banner.updated_successfully'));

        return redirect(route('admin.banners.index'));
    }

    /**
     * Remove the specified Banner from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $banner = $this->bannerRepository->findWithoutFail($id);

        if (empty($banner)) {
            Flash::error(trans('messages.banner.not_found'));

            return redirect(route('admin.banners.index'));
        }

        $this->bannerRepository->delete($id);

        Flash::success(trans('messages.banner.deleted_successfully'));

        return redirect(route('admin.banners.index'));
    }

    /**
     * Change banner orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOrder(Request $request)
    {
        $orders = $request->get('orders', []);
        /** @var \App\Models\Banner $bannerInstance */
        $bannerInstance = Banner::getInstance();

        foreach ($orders as $id => $order) {
            Banner::where($bannerInstance->getKeyName(), $id)
                ->update([
                    'order' => $order
                ]);
        }

        return $this->sendResponse(null, 'Successfully.');
    }

}
