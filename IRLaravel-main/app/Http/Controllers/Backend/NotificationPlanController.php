<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateNotificationPlanRequest;
use App\Http\Requests\UpdateNotificationPlanRequest;
use App\Repositories\NotificationPlanRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class NotificationPlanController extends BaseController
{
    /** @var  NotificationPlanRepository */
    private $notificationPlanRepository;

    public function __construct(NotificationPlanRepository $notificationPlanRepo)
    {
        parent::__construct();

        $this->notificationPlanRepository = $notificationPlanRepo;
    }

    /**
     * Display a listing of the NotificationPlan.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->notificationPlanRepository->pushCriteria(new RequestCriteria($request));
        $notificationPlans = $this->notificationPlanRepository->all();

        return view('admin.notification_plans.index')
            ->with('notificationPlans', $notificationPlans);
    }

    /**
     * Show the form for creating a new NotificationPlan.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.notification_plans.create');
    }

    /**
     * Store a newly created NotificationPlan in storage.
     *
     * @param CreateNotificationPlanRequest $request
     *
     * @return Response
     */
    public function store(CreateNotificationPlanRequest $request)
    {
        $input = $request->all();

        $notificationPlan = $this->notificationPlanRepository->create($input);

        Flash::success(trans('notification_plan.message_saved_successfully'));

        return redirect(route('admin.notificationPlans.index'));
    }

    /**
     * Display the specified NotificationPlan.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            Flash::error(trans('notification_plan.not_found'));

            return redirect(route('admin.notificationPlans.index'));
        }

        return view('admin.notification_plans.show')->with('notificationPlan', $notificationPlan);
    }

    /**
     * Show the form for editing the specified NotificationPlan.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            Flash::error(trans('notification_plan.not_found'));

            return redirect(route('admin.notificationPlans.index'));
        }

        return view('admin.notification_plans.edit')->with('notificationPlan', $notificationPlan);
    }

    /**
     * Update the specified NotificationPlan in storage.
     *
     * @param  int              $id
     * @param UpdateNotificationPlanRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationPlanRequest $request)
    {
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            Flash::error(trans('notification_plan.not_found'));

            return redirect(route('admin.notificationPlans.index'));
        }

        $notificationPlan = $this->notificationPlanRepository->update($request->all(), $id);

        Flash::success(trans('notification_plan.message_updated_successfully'));

        return redirect(route('admin.notificationPlans.index'));
    }

    /**
     * Remove the specified NotificationPlan from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $notificationPlan = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notificationPlan)) {
            Flash::error(trans('notification_plan.not_found'));

            return redirect(route('admin.notificationPlans.index'));
        }

        $this->notificationPlanRepository->delete($id);

        Flash::success(trans('notification_plan.message_deleted_successfully'));

        return redirect(route('admin.notificationPlans.index'));
    }
}
