<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateSettingPrintRequest;
use App\Http\Requests\UpdateSettingPrintRequest;
use App\Repositories\SettingPrintRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SettingPrintController extends BaseController
{
    /** @var  SettingPrintRepository */
    private $settingPrintRepository;

    public function __construct(SettingPrintRepository $settingPrintRepo)
    {
        parent::__construct();

        $this->settingPrintRepository = $settingPrintRepo;
    }

    /**
     * Display a listing of the SettingPrint.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->settingPrintRepository->pushCriteria(new RequestCriteria($request));
        $settingPrints = $this->settingPrintRepository->all();

        return view('admin.setting_prints.index')
            ->with('settingPrints', $settingPrints);
    }

    /**
     * Show the form for creating a new SettingPrint.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.setting_prints.create');
    }

    /**
     * Store a newly created SettingPrint in storage.
     *
     * @param CreateSettingPrintRequest $request
     *
     * @return Response
     */
    public function store(CreateSettingPrintRequest $request)
    {
        $input = $request->all();

        $settingPrint = $this->settingPrintRepository->create($input);

        Flash::success(trans('setting_print.message_saved_successfully'));

        return redirect(route('admin.settingPrints.index'));
    }

    /**
     * Display the specified SettingPrint.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            Flash::error(trans('setting_print.not_found'));

            return redirect(route('admin.settingPrints.index'));
        }

        return view('admin.setting_prints.show')->with('settingPrint', $settingPrint);
    }

    /**
     * Show the form for editing the specified SettingPrint.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            Flash::error(trans('setting_print.not_found'));

            return redirect(route('admin.settingPrints.index'));
        }

        return view('admin.setting_prints.edit')->with('settingPrint', $settingPrint);
    }

    /**
     * Update the specified SettingPrint in storage.
     *
     * @param  int              $id
     * @param UpdateSettingPrintRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSettingPrintRequest $request)
    {
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            Flash::error(trans('setting_print.not_found'));

            return redirect(route('admin.settingPrints.index'));
        }

        $settingPrint = $this->settingPrintRepository->update($request->all(), $id);

        Flash::success(trans('setting_print.message_updated_successfully'));

        return redirect(route('admin.settingPrints.index'));
    }

    /**
     * Remove the specified SettingPrint from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $settingPrint = $this->settingPrintRepository->findWithoutFail($id);

        if (empty($settingPrint)) {
            Flash::error(trans('setting_print.not_found'));

            return redirect(route('admin.settingPrints.index'));
        }

        $this->settingPrintRepository->delete($id);

        Flash::success(trans('setting_print.message_deleted_successfully'));

        return redirect(route('admin.settingPrints.index'));
    }
}
