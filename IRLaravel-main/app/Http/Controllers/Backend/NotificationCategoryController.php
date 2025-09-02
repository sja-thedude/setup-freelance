<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateNotificationCategoryRequest;
use App\Http\Requests\UpdateNotificationCategoryRequest;
use App\Repositories\NotificationCategoryRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class NotificationCategoryController extends BaseController
{
    /** @var  NotificationCategoryRepository */
    private $notificationCategoryRepository;

    public function __construct(NotificationCategoryRepository $notificationCategoryRepo)
    {
        parent::__construct();

        $this->notificationCategoryRepository = $notificationCategoryRepo;
    }

    /**
     * Display a listing of the NotificationCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->notificationCategoryRepository->pushCriteria(new RequestCriteria($request));
        $notificationCategories = $this->notificationCategoryRepository->all();

        return view('admin.notification_categories.index')
            ->with('notificationCategories', $notificationCategories);
    }

    /**
     * Show the form for creating a new NotificationCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.notification_categories.create');
    }

    /**
     * Store a newly created NotificationCategory in storage.
     *
     * @param CreateNotificationCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateNotificationCategoryRequest $request)
    {
        $input = $request->all();

        $notificationCategory = $this->notificationCategoryRepository->create($input);

        Flash::success(trans('notification_category.message_saved_successfully'));

        return redirect(route('admin.notificationCategories.index'));
    }

    /**
     * Display the specified NotificationCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            Flash::error(trans('notification_category.not_found'));

            return redirect(route('admin.notificationCategories.index'));
        }

        return view('admin.notification_categories.show')->with('notificationCategory', $notificationCategory);
    }

    /**
     * Show the form for editing the specified NotificationCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            Flash::error(trans('notification_category.not_found'));

            return redirect(route('admin.notificationCategories.index'));
        }

        return view('admin.notification_categories.edit')->with('notificationCategory', $notificationCategory);
    }

    /**
     * Update the specified NotificationCategory in storage.
     *
     * @param  int              $id
     * @param UpdateNotificationCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationCategoryRequest $request)
    {
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            Flash::error(trans('notification_category.not_found'));

            return redirect(route('admin.notificationCategories.index'));
        }

        $notificationCategory = $this->notificationCategoryRepository->update($request->all(), $id);

        Flash::success(trans('notification_category.message_updated_successfully'));

        return redirect(route('admin.notificationCategories.index'));
    }

    /**
     * Remove the specified NotificationCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $notificationCategory = $this->notificationCategoryRepository->findWithoutFail($id);

        if (empty($notificationCategory)) {
            Flash::error(trans('notification_category.not_found'));

            return redirect(route('admin.notificationCategories.index'));
        }

        $this->notificationCategoryRepository->delete($id);

        Flash::success(trans('notification_category.message_deleted_successfully'));

        return redirect(route('admin.notificationCategories.index'));
    }
}
