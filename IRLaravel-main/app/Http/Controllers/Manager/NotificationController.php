<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\CreateNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\RestaurantCategory;
use Illuminate\Http\Request;
use Flash;
use Response;
use App\Repositories\NotificationRepository;
use App\Repositories\NotificationPlanRepository;

class NotificationController extends BaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;
    private $notificationPlanRepository;

    public function __construct(NotificationRepository $notificationRepo, NotificationPlanRepository $notificationPlanRepo)
    {
        parent::__construct();

        $this->notificationRepository = $notificationRepo;
        $this->notificationPlanRepository = $notificationPlanRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $types = RestaurantCategory::getAll();
        $model = $this->notificationPlanRepository->getLists($request, Notification::MANAGER, $this->perPage, $this->tmpWorkspace->id);

        return view($this->guard.'.notifications.index')
            ->with(compact(
                'model',
                'types'
            ));
    }

    /**
     * Show the form for creating a new Notification.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * @param CreateNotificationRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateNotificationRequest $request)
    {
        $input = $request->all();
        $input['platform'] = Notification::MANAGER;
        $input['workspace_id'] = $this->tmpWorkspace->id;
        $input['gender_dest_male'] = !empty($request->gender_dest_male) ? Notification::CHECKED_GENDER : Notification::UNCHECKED_GENDER;
        $input['gender_dest_female'] = !empty($request->gender_dest_female) ? Notification::CHECKED_GENDER : Notification::UNCHECKED_GENDER;
        $timezone = !empty($input['timezone']) ? $input['timezone'] : 'UTC';

        if ($request->sent_time) {
            $input['send_datetime'] = $input['sent_time'];
        } else {
            $input['send_datetime'] = !empty($request->send_now)
                ? date(config('datetime.timeFormat2'))
                : Helper::convertDateTimeToUTC($request->send_datetime, $timezone);
            $input['sent_time'] = $input['send_datetime'];
        }

        /** @var \App\Models\NotificationPlan $notificationPlan */
        $notificationPlan = $this->notificationPlanRepository->create($input);

        if (empty($notificationPlan) || empty($notificationPlan->id)) {
            throw new \Exception('Unable to create new Notification Plan');
        }

        if ($request->get('send_now')) {
            // Apply notification to users
            $input['notification_plan_id'] = $notificationPlan->id;
            $input['template_id'] = $notificationPlan->workspace_id;
            $this->notificationRepository->applyNotification($input);

            // Mark is send plan
            $notificationPlan->is_sent = true;
            $notificationPlan->save();
        }

        if ($request->ajax()) {
            $message = (!empty($request->send_datetime) && $request->send_now == 0)
                ? trans('user.plan_sent_message_successfully')
                : trans('user.sent_message_successfully');
            return $this->sendResponse($notificationPlan, $message);
        }

        Flash::success('Notification is saved successfully.');

        return redirect(route('admin.notifications.index'));
    }

    /**
     * Display the specified Notification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            Flash::error('Notification not found');

            return redirect(route('admin.notifications.index'));
        }

        return view('admin.notifications.show')->with('notification', $notification);
    }

    /**
     * Show the form for editing the specified Notification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            Flash::error('Notification not found');

            return redirect(route('admin.notifications.index'));
        }

        return view('admin.notifications.edit')->with('notification', $notification);
    }

    /**
     * Update the specified Notification in storage.
     *
     * @param  int              $id
     * @param UpdateNotificationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationRequest $request)
    {
        $notification = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notification)) {
            Flash::error('Notification not found');

            return redirect(route('admin.notifications.index'));
        }

        $notification = $this->notificationPlanRepository->update($request->all(), $id);

        Flash::success('Notification is updated successfully.');

        return redirect(route('admin.notifications.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $notification = $this->notificationPlanRepository->findWithoutFail($id);

        if (empty($notification)) {
            Flash::error(trans('notification.not_found'));

            return redirect(route($this->guard.'.notifications.index'));
        }

        $this->notificationPlanRepository->delete($id);

        $response = array(
            'status' => 'success',
            'message' => trans('notification.deleted_confirm')
        );

        return response()->json($response);
    }
}
