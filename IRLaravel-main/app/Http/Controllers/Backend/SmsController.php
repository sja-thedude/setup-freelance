<?php

namespace App\Http\Controllers\Backend;

use App\Models\Workspace;
use App\Repositories\SmsRepository;
use Illuminate\Http\Request;
use Response;

class SmsController extends BaseController
{
    /** @var  smsRepository */
    private $smsRepository;

    public function __construct(SmsRepository $smsRepo)
    {
        parent::__construct();

        $this->smsRepository = $smsRepo;
    }

    /**
     * Display a listing of the sms.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $workspaces = Workspace::all();
        $sms = $this->smsRepository->getList($request);

        return view('admin.sms.index')->with(compact('sms', 'workspaces'));
    }
}
