<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use App\Repositories\NotificationPlanRepository;
use View;
use App\Services\Socket;

class NotificationController extends BaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;
    private $notificationPlanRepository;
    protected $socket;

    public function __construct(NotificationRepository $notificationRepo, NotificationPlanRepository $notificationPlanRepo)
    {
        parent::__construct();

        $this->notificationRepository = $notificationRepo;
        $this->notificationPlanRepository = $notificationPlanRepo;
        $this->socket = new Socket();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = session('workspace_'.$workspaceSlug);
        $request = $request->merge([
            'workspace_id' => !empty($workspace) ? $workspace->id : null,
        ]);
        $userId = !auth()->guest() ? auth()->user()->id : null;

        $notification = $this->notificationRepository->getNotificationByUser($request, $userId);

        $html = View::make($this->guard . '.partials.notifications')->with("notification", $notification)->render();

        if($request->ajax()) {
            return $this->sendResponse($html, trans('messages.success'));
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function show(Request $request, $id)
    {
        $userId = !auth()->guest() ? auth()->user()->id : null;

        $notification = $this->notificationRepository->findWithoutFail($id);

        $html = View::make($this->guard . '.partials.notification-detail')->with("notification", $notification)->render();

        if($request->ajax()) {
            $notification->status = Notification::ACTIVE;
            $notification->save();

            //Push notification
            $this->socket->emit('notification', array(
                'count' => Helper::displayNotificationNumberByUser(),
                'userId' => $userId
            ));

            return $this->sendResponse($html, trans('messages.success'));
        }
    }

    /**
     * @param Request $request
     */
    public function test_socket(Request $request)
    {
        // Notice for app to reload list
        $this->socket->emit('notification', array(
            'count' => 5,
            'userId' => 1
        ));

        echo 'Done'; exit();
    }
}
