<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\CreateOptionRequest;
use App\Http\Requests\UpdateOptionRequest;
use App\Repositories\CartOptionItemRepository;
use App\Repositories\CategoryOptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MediaRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionItemRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductLabelRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\VatRepository;
use Flash;
use Illuminate\Http\Request;
use Log;

/**
 * Class OptionController
 *
 * @package App\Http\Controllers\Manager
 */
class OptionController extends BaseController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * @var CategoryOptionRepository
     */
    private $categoryOptionRepository;

    /**
     * @var ProductOptionRepository
     */
    private $productOptionRepository;

    /**
     * @var OpenTimeslotRepository
     */
    private $openTimeslotRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var VatRepository
     */
    private $vatRepository;

    /**
     * @var ProductAllergenenRepository
     */
    private $productAllergenenRepository;

    /**
     * @var ProductLabelRepository
     */
    private $productLabelRepository;

    /**
     * @var OptionItemRepository
     */
    private $optionItemRepository;

    /**
     * @var CartOptionItemRepository
     */
    private $cartOptionItemRepository;

    /**
     * @var SettingConnectorRepository
     */
    private $settingConnectorRepository;

    /**
     * OptionController constructor.
     *
     * @param CategoryRepository          $categoryRepository
     * @param OptionRepository            $optionRepository
     * @param CategoryOptionRepository    $categoryOptionRepository
     * @param ProductOptionRepository     $productOptionRepository
     * @param ProductAllergenenRepository $productAllergenenRepository
     * @param ProductLabelRepository      $productLabelRepository
     * @param OpenTimeslotRepository      $openTimeslotRepository
     * @param MediaRepository             $mediaRepository
     * @param ProductRepository           $productRepository
     * @param VatRepository               $vatRepository
     * @param OptionItemRepository        $optionItemRepository
     * @param CartOptionItemRepository    $cartOptionItemRepository
     * @param SettingConnectorRepository  $settingConnectorRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        OptionRepository $optionRepository,
        CategoryOptionRepository $categoryOptionRepository,
        ProductOptionRepository $productOptionRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        ProductLabelRepository $productLabelRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        MediaRepository $mediaRepository,
        ProductRepository $productRepository,
        VatRepository $vatRepository,
        OptionItemRepository $optionItemRepository,
        CartOptionItemRepository $cartOptionItemRepository,
        SettingConnectorRepository $settingConnectorRepository
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->optionRepository = $optionRepository;
        $this->categoryOptionRepository = $categoryOptionRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->productLabelRepository = $productLabelRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->vatRepository = $vatRepository;
        $this->vatRepository = $vatRepository;
        $this->optionItemRepository = $optionItemRepository;
        $this->cartOptionItemRepository = $cartOptionItemRepository;
        $this->settingConnectorRepository = $settingConnectorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        return view('manager.options.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        return response()->json([
            'code' => 200,
            'data' => view('manager.options.partials.form', [
                'action'      => route($this->guard . '.options.store'),
                'method'      => 'POST',
                'idForm'      => 'create_option',
                'titleModal'  => trans('option.nieuwe_ategorie'),
            ])->render(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function createItem()
    {
        return response()->json([
            'code' => 200,
            'data' => view('manager.options.partials.item', [
                'key'  => rand(1, 99999999),
                'item' => new \App\Models\OptionItem()
            ])->render(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateOptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOptionRequest $request)
    {
        try {
            \DB::beginTransaction();

            $optionItems = array();
            $data = $request->all();

            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['type'] = $data['min'] > 0;

            $option = $this->optionRepository->create($data);

            $items = isset($data['items']) ? $data['items'] : array();
            $master = isset($data['master']) ? $data['master'] : NULL;

            $order = 1;
            foreach ($items as $k => $item) {
                $optionItems[$k]['master']    = !is_null($master) && !is_bool($master) && (int) $master === $k;
                $optionItems[$k]['available'] = isset($item['available']) ?? false;
                $optionItems[$k]['currency']  = "EUR";
                $optionItems[$k]['opties_id'] = $option->id;
                $optionItems[$k]['price']     = $item['price'];
                $optionItems[$k]['name']      = $item['name'];
                $optionItems[$k]['order']     = $order;
                $order++;
            }

            $this->optionItemRepository->saveMany($optionItems);

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('option.created_successfully'));
            }

            Flash::success(trans('option.created_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int     $optionId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request, int $optionId)
    {
        $option = $this->optionRepository
            ->with('optionItems')
            ->findWithoutFail($optionId);

        if (empty($option)) {
            Flash::error(trans('option.not_found'));
            return redirect(route($this->guard . '.options.index'));
        }

        return response()->json([
            'code' => 200,
            'data' => view('manager.options.partials.form', [
                'option'     => $option,
                'action'     => route($this->guard . '.options.update', $option->id),
                'method'     => 'PUT',
                'idForm'     => 'update_option',
                'titleModal' => trans('option.categorie_wijzigen'),
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOptionRequest $request
     * @param int                  $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOptionRequest $request, int $optionId)
    {
        $option = $this->optionRepository->findWithoutFail($optionId);

        if (empty($option)) {
            Flash::error(trans('option.not_found'));
            return redirect(route($this->guard . '.options.index'));
        }

        try {
            \DB::beginTransaction();

            $data = $request->all();

            $data['workspace_id'] = $this->tmpUser->workspace_id;
            $data['type'] = $data['min'] > 0;

            $option = $this->optionRepository->with('optionItems')
                ->findWhere(['id' => $optionId])
                ->first();

            $option->update($data);
            $idsOptionItem = $option->optionItems->pluck('id')->toArray();

            $items = isset($data['items']) ? $data['items'] : array();
            $master = isset($data['master']) ? $data['master'] : FALSE;

            $order = 1;
            foreach ($items as $k => $item) {
                $optionItem              = array();
                $optionItem['master']    = !is_null($master) && !is_bool($master) && (int) $master === $k;
                $optionItem['available'] = isset($item['available']) ?? false;
                $optionItem['currency']  = "EUR";
                $optionItem['price']     = $item['price'];
                $optionItem['name']      = $item['name'];
                $optionItem['order']     = $order;

                if ($item['idOptionItem']) {
                    $key = array_search($item['idOptionItem'], $idsOptionItem);
                    unset($idsOptionItem[$key]);

                    $this->optionItemRepository->updateAndWhere([
                        'id' => $item['idOptionItem']
                    ], $optionItem);

                } else {
                    $optionItem['opties_id'] = $option->id;
                    $this->optionItemRepository->create($optionItem);
                }

                $order++;
            }

            foreach ($idsOptionItem as $id) {
                $this->optionItemRepository->deleteWhere(['id' => $id]);

                // Delete cart has option-item
                $this->cartOptionItemRepository->deleteWhere([
                    'workspace_id'  => $this->tmpUser->workspace_id,
                    'optie_id'      => $optionId,
                    'optie_item_id' => $id,
                ]);
            }

            \DB::commit();

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('option.updated_successfully'));
            }

            Flash::success(trans('option.updated_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $option = $this->optionRepository->findWithoutFail($id);

            if (empty($option)) {
                Flash::error(trans('option.not_found'));
                return redirect(route($this->guard . '.options.index'));
            }

            $this->optionRepository->delete($id);

            $this->cartOptionItemRepository->deleteWhere([
                'workspace_id' => $this->tmpUser->workspace_id,
                'optie_id'     => $id,
            ]);

            $this->categoryOptionRepository->deleteWhere(['opties_id' => $id]);
            $this->productOptionRepository->deleteWhere(['opties_id' => $id]);

            \DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => trans('option.deleted_confirm'),
            ]);

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getTraceAsString());

            return response()->json([
                'status'  => "failed",
                'message' => $exc->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrder(Request $request)
    {
        try {
            \DB::beginTransaction();

            $orders = $request->order;

            foreach ($orders as $no => $id) {
                $this->optionRepository->update(['order' => $no + 1], $id);
            }

            \DB::commit();

            return response()->json([
                'code'    => 200,
                'message' => 'Success!',
                'data'    => [],
            ]);

        } catch (\Exception $exc) {
            \DB::rollback();

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int     $optionId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function itemsReferences(Request $request, int $optionId)
    {
        $option = $this->optionRepository
            ->with('optionItems')
            ->findWithoutFail($optionId);

        if (empty($option)) {
            Flash::error(trans('option.not_found'));
            return redirect(route($this->guard . '.options.index'));
        }

        $optionItemIds = $option->items->pluck('id');
        $connectorsList = $this->getConnectorsList();

        $optionItemReferences = null;
        if(!empty($connectorsList)) {
            $optionItemReferences = $this->optionItemRepository->getOptionItemReferencesByWorkspaceAndLocalIds($this->tmpWorkspace->id, $optionItemIds);
        }

        return response()->json([
            'code' => 200,
            'data' => view('manager.options.partials.items_references', [
                'option'     => $option,
                'action'     => route($this->guard . '.options.updateItemsReferences', $option->id),
                'method'     => 'PUT',
                'idForm'     => 'update_option',
                'titleModal' => trans('option.categorie_wijzigen'),
                'connectorsList' => $connectorsList,
                'optionItemReferences' => $optionItemReferences
            ])->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItemsReferences(Request $request, int $optionId)
    {
        $option = $this->optionRepository
            ->with('optionItems')
            ->findWithoutFail($optionId);

        if (empty($option)) {
            Flash::error(trans('option.not_found'));
            return redirect(route($this->guard . '.options.index'));
        }

        try {
            $data = $request->all();

            $connectorsList = $this->getConnectorsList();
            if(!empty($connectorsList)) {
                \DB::beginTransaction();
                $this->optionItemRepository->updateOrCreateOptionItemReferences($data, $this->tmpWorkspace->id, $connectorsList, $option);
                \DB::commit();
            }

            if ($request->ajax()) {
                return $this->sendResponse($data, trans('option.updated_successfully'));
            }

            Flash::success(trans('option.updated_successfully'));

        } catch (\Exception $exc) {
            \DB::rollback();
            Log::error($exc->getMessage());

            return response()->json([
                'code'    => 500,
                'message' => $exc,
            ]);
        }
    }

    /**
     * @return null
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }
}
