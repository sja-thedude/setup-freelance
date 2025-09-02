<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateRestaurantCategoryRequest;
use App\Http\Requests\UpdateRestaurantCategoryRequest;
use App\Repositories\RestaurantCategoryRepository;
use Illuminate\Http\Request;
use Flash;

class RestaurantCategoryController extends BaseController
{
    /** @var  RestaurantCategoryRepository */
    private $restaurantCategoryRepository;

    public function __construct(RestaurantCategoryRepository $restaurantCategoryRepo)
    {
        parent::__construct();

        $this->restaurantCategoryRepository = $restaurantCategoryRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $model = $this->restaurantCategoryRepository->getLists($request, $this->perPage);

        return view('admin.type_zaak.index')->with(compact(
                'model'
            ));
    }

    /**
     * @param CreateRestaurantCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRestaurantCategoryRequest $request)
    {
        $input = $request->all();

        $restaurantCategory = $this->restaurantCategoryRepository->create($input);

        if($request->ajax()) {
            return $this->sendResponse($restaurantCategory, trans('type_zaak.created_successfully'));
        }
        
        Flash::success(trans('restaurant_category.message_saved_successfully'));

        return redirect(route('admin.restaurantCategories.index'));
    }

    /**
     * @param UpdateRestaurantCategoryRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateRestaurantCategoryRequest $request, $id)
    {
        $restaurantCategory = $this->restaurantCategoryRepository->findWithoutFail($id);

        if (empty($restaurantCategory)) {
            Flash::error(trans('type_zaak.not_found'));

            return redirect(route('admin.type_zaak.index'));
        }

        $restaurantCategory = $this->restaurantCategoryRepository->update($request->all(), $id);
        
        if($request->ajax()) {
            return $this->sendResponse($restaurantCategory, trans('type_zaak.updated_confirm'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->restaurantCategoryRepository->delete($id);
        $response = array(
            'status' => 'success',
            'message' => trans('type_zaak.deleted_confirm')
        );

        return response()->json($response);
    }
}
