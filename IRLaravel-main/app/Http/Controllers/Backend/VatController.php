<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateVatRequest;
use App\Http\Requests\UpdateVatRequest;
use App\Repositories\VatRepository;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;
use Flash;
use Response;

class VatController extends BaseController
{
    private $vatRepository;
    private $countryRepository;
    
    public function __construct(VatRepository $vatRepo, CountryRepository $countryRepo)
    {
        parent::__construct();

        $this->vatRepository = $vatRepo;
        $this->countryRepository = $countryRepo;
    }

    /**
     * Display a listing of the Vat.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {        
        $countries = $this->countryRepository->all();

        if($request->ajax()) {
            $vats = $this->vatRepository->getListByCountry($request->get('country_id'));
            $view = view('admin.vats.partials.fields', compact('vats'))->render();
            return $this->sendResponse(compact('view'), trans('vat.save_success'));
        } else {
            $belgium = $this->countryRepository->makeModel()->where('code', 'be')->first(); // Default BE
            $countryId = $belgium->id;
            $vats = $this->vatRepository->getListByCountry($countryId);       
        }
        
        return view('admin.vats.index')
            ->with(compact('vats', 'countries', 'countryId'));
    }

    /**
     * Show the form for creating a new Vat.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.vats.create');
    }

    /**
     * Store a newly created Vat in storage.
     *
     * @param CreateVatRequest $request
     *
     * @return Response
     */
    public function store(CreateVatRequest $request)
    {
        $input = $request->all();
        $countryId = $input['country_id'];
        $vat = $this->vatRepository->updateOrCreateVat($input);

        if($request->ajax()) {
            $vats = $this->vatRepository->getListByCountry($countryId);
            $view = view('admin.vats.partials.fields', compact('vats'))->render();
            return $this->sendResponse(compact('view'), trans('vat.save_success'));
        }
        
        Flash::success(trans('vat.save_success'));
        return redirect(route('admin.vats.index'));
    }

    /**
     * Display the specified Vat.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            Flash::error(trans('vat.not_found'));

            return redirect(route('admin.vats.index'));
        }

        return view('admin.vats.show')->with('vat', $vat);
    }

    /**
     * Show the form for editing the specified Vat.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            Flash::error(trans('vat.not_found'));

            return redirect(route('admin.vats.index'));
        }

        return view('admin.vats.edit')->with('vat', $vat);
    }

    /**
     * Update the specified Vat in storage.
     *
     * @param  int              $id
     * @param UpdateVatRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateVatRequest $request)
    {
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            Flash::error(trans('vat.not_found'));

            return redirect(route('admin.vats.index'));
        }

        $vat = $this->vatRepository->update($request->all(), $id);

        Flash::success(trans('vat.message_updated_successfully'));

        return redirect(route('admin.vats.index'));
    }

    /**
     * Remove the specified Vat from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $vat = $this->vatRepository->findWithoutFail($id);

        if (empty($vat)) {
            Flash::error(trans('vat.not_found'));

            return redirect(route('admin.vats.index'));
        }

        $this->vatRepository->delete($id);

        Flash::success(trans('vat.message_deleted_successfully'));

        return redirect(route('admin.vats.index'));
    }
}
