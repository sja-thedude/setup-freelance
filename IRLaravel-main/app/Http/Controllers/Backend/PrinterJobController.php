<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreatePrinterJobRequest;
use App\Http\Requests\UpdatePrinterJobRequest;
use App\Repositories\PrinterJobRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PrinterJobController extends BaseController
{
    /** @var  PrinterJobRepository */
    private $printerJobRepository;

    public function __construct(PrinterJobRepository $printerJobRepo)
    {
        parent::__construct();

        $this->printerJobRepository = $printerJobRepo;
    }

    /**
     * Display a listing of the PrinterJob.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->printerJobRepository->pushCriteria(new RequestCriteria($request));
        $printerJobs = $this->printerJobRepository->all();

        return view('admin.printer_jobs.index')
            ->with('printerJobs', $printerJobs);
    }

    /**
     * Show the form for creating a new PrinterJob.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.printer_jobs.create');
    }

    /**
     * Store a newly created PrinterJob in storage.
     *
     * @param CreatePrinterJobRequest $request
     *
     * @return Response
     */
    public function store(CreatePrinterJobRequest $request)
    {
        $input = $request->all();

        $printerJob = $this->printerJobRepository->create($input);

        Flash::success(trans('printer_job.message_saved_successfully'));

        return redirect(route('admin.printerJobs.index'));
    }

    /**
     * Display the specified PrinterJob.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $printerJob = $this->printerJobRepository->findWithoutFail($id);

        if (empty($printerJob)) {
            Flash::error(trans('printer_job.not_found'));

            return redirect(route('admin.printerJobs.index'));
        }

        return view('admin.printer_jobs.show')->with('printerJob', $printerJob);
    }

    /**
     * Show the form for editing the specified PrinterJob.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $printerJob = $this->printerJobRepository->findWithoutFail($id);

        if (empty($printerJob)) {
            Flash::error(trans('printer_job.not_found'));

            return redirect(route('admin.printerJobs.index'));
        }

        return view('admin.printer_jobs.edit')->with('printerJob', $printerJob);
    }

    /**
     * Update the specified PrinterJob in storage.
     *
     * @param  int              $id
     * @param UpdatePrinterJobRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePrinterJobRequest $request)
    {
        $printerJob = $this->printerJobRepository->findWithoutFail($id);

        if (empty($printerJob)) {
            Flash::error(trans('printer_job.not_found'));

            return redirect(route('admin.printerJobs.index'));
        }

        $printerJob = $this->printerJobRepository->update($request->all(), $id);

        Flash::success(trans('printer_job.message_updated_successfully'));

        return redirect(route('admin.printerJobs.index'));
    }

    /**
     * Remove the specified PrinterJob from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $printerJob = $this->printerJobRepository->findWithoutFail($id);

        if (empty($printerJob)) {
            Flash::error(trans('printer_job.not_found'));

            return redirect(route('admin.printerJobs.index'));
        }

        $this->printerJobRepository->delete($id);

        Flash::success(trans('printer_job.message_deleted_successfully'));

        return redirect(route('admin.printerJobs.index'));
    }
}
