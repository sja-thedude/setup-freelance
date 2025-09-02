<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\PrinterJobRepository;
use Illuminate\Http\Request;

class PrintJobController extends BaseController
{
    private $printerJobRepository;

    public function __construct(PrinterJobRepository $printerJobRepo)
    {
        parent::__construct();

        $this->printerJobRepository = $printerJobRepo;
    }

    public function index(Request $request)
    {
        $workspaces = \App\Models\Workspace::all();
        $jobs = $this->printerJobRepository->getList($request);

        return view('admin.print_jobs.index')->with(compact('jobs', 'workspaces'));
    }

    public function cancel(Request $request, $id)
    {
        $this->printerJobRepository->cancel($id);

        return redirect()->back();
    }
}
