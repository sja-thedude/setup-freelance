<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Order as OrderHelper;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Email;
use App\Models\Order;
use App\Models\SettingPrint;
use App\Repositories\ContactRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PrinterJobRepository;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\SmsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Response;

class OrderController extends BaseController
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var PrinterJobRepository
     */
    private $printerJobRepository;

    /**
     * @var SettingConnectorRepository
     */
    private $settingConnectorRepository;

    /** @var  smsRepository */
    private $smsRepository;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @param OrderRepository $orderRepo
     * @param PrinterJobRepository $printerJobRepo
     * @param SettingConnectorRepository $settingConnectorRepository
     * @param SmsRepository $smsRepo
     * @param ContactRepository $contactRepo
     */
    public function __construct(
        OrderRepository $orderRepo,
        PrinterJobRepository $printerJobRepo,
        SettingConnectorRepository $settingConnectorRepository,
        SmsRepository $smsRepo,
        ContactRepository $contactRepo
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepo;
        $this->printerJobRepository = $printerJobRepo;
        $this->settingConnectorRepository = $settingConnectorRepository;
        $this->smsRepository = $smsRepo;
        $this->contactRepository = $contactRepo;
    }

    /**
     * Display a listing of the Order.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $workspaceId = $this->tmpWorkspace->id;
        $optionSetting = $this->orderRepository->getOptionFromSettingPreference($workspaceId);
        $option = !empty($optionSetting->option) ? $optionSetting->option : null;
        $orders = [];
        $autoloadAjax = 1;
        $isShowSticker = !empty($this->tmpWorkspace) ? $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::STICKER)->first() : null;

        if(!empty($input['date_range'])) {
            $autoloadAjax = 0;
            $orders = $this->orderRepository->getListOrders($request, $workspaceId, false, [], 100);
        }

        $orderIds = (!empty($orders)) ? $orders->pluck('id') : [];
        $connectorsList = $this->getConnectorsList();

        $orderReferences = null;
        if(!empty($connectorsList) && !empty($orderIds)) {
            $orderReferences = $this->orderRepository->getOrderReferencesByWorkspaceAndLocalIds($this->tmpWorkspace->id, $orderIds);
        }

        $totalOrder = !empty($orders) ? $orders->sum('total_order') : 0;
        $data = array_merge(compact(
            'orders', 'totalOrder', 'option',
            'workspaceId', 'autoloadAjax',
            'isShowSticker', 'connectorsList', 'orderReferences'
        ), $input);

        if($request->ajax()) {
            $view = view('manager.orders.partials.table', $data)->render();
            return $this->sendResponse(compact('view', 'totalOrder'), 'success');
        }

        return view('manager.orders.index', $data);
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
        return view('manager.orders.create');
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        $input = $request->all();

        $order = $this->orderRepository->create($input);

        Flash::success('Order is saved successfully.');

        return redirect(route('manager.orders.index'));
    }

    /**
     * Display the specified Order.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error('Order not found');

            return redirect(route('manager.orders.index'));
        }

        return view('manager.orders.show')->with('order', $order);
    }

    /**
     * Show the form for editing the specified Order.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error('Order not found');

            return redirect(route('manager.orders.index'));
        }

        return view('manager.orders.edit')->with('order', $order);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param  int              $id
     * @param UpdateOrderRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrderRequest $request)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error('Order not found');

            return redirect(route('manager.orders.index'));
        }

        $order = $this->orderRepository->update($request->all(), $id);

        Flash::success('Order is updated successfully.');

        return redirect(route('manager.orders.index'));
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error('Order not found');

            return redirect(route('manager.orders.index'));
        }

        $this->orderRepository->delete($id);

        Flash::success('Order is deleted successfully.');

        return redirect(route('manager.orders.index'));
    }

    public function markNoShow($id) {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.mark_no_show_failed'));
        }

        $order->no_show = true;
        $order->save();

        return $this->sendResponse(null, trans('order.mark_no_show_success'));
    }

    public function manualConfirmed($id) {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.mark_no_show_failed'));
        }

        if ($order->isTableOrdering()) {
            $highestIdSubOrder = $order->where('parent_id', $order->id)
                ->orderBy('id', 'desc')
                ->first();

            if($highestIdSubOrder) {
                $highestIdSubOrder->table_last_person = true;
                $highestIdSubOrder->save();
            }

            $isShowSticker = !empty($this->tmpWorkspace) ? $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::STICKER)->first() : null;
            $confirmedDropdown = view('manager.orders.partials.table_item_actions', compact('order', 'isShowSticker'))->render();
        }

        $order->table_last_person = true;
        $order->save();

        $types = config('print.all_type');
        unset($types['a4']);

        foreach ($types as $type) {
            if($type == config('print.all_type.kassabon') && $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
                if(!$order->groupOrders->isEmpty()) {
                    foreach($order->groupOrders as $subOrder) {
                        $this->orderRepository->printItemByType($subOrder->id, $subOrder, $type);
                    }
                }
            } else {
                $this->orderRepository->printItemByType($order->id, $order, $type);
            }
        }

        return $this->sendResponse(compact('confirmedDropdown'), trans('order.manual_confirm_success'));
    }

    public function manualCheckedFullyPaidCash($id) {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.mark_no_show_failed'));
        }

        if ($order->isTableOrdering()) {
            $subOrders = $order->where('parent_id', $order->id)->whereNotIn('status', [
                Order::PAYMENT_STATUS_CANCELLED,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_EXPIRED,
            ])->get();
            $totalPrice = $subOrders->sum('total_price');
            if(!$subOrders->isEmpty()) {
                foreach($subOrders as $subOrder) {
                    $subOrder->total_paid = (float)$subOrder->total_price;
                    $subOrder->status = Order::PAYMENT_STATUS_PAID;
                    $subOrder->save();
                }
            }
            $order->total_price = $totalPrice;
        }

        $order->total_paid = $order->total_price;
        $order->status = Order::PAYMENT_STATUS_PAID;
        $order->save();

        $responseData = [
            'total_paid' => '€'.$order->calculate_total_paid,
        ];

        return $this->sendResponse($responseData, trans('order.manual_checked_fully_paid_cash_success'));
    }

    public function printItem(Request $request, $type, $orderId) {
        $order = $this->orderRepository->findWithoutFail($orderId);

        if($type == config('print.all_type.kassabon') && $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
            if(!$order->groupOrders->isEmpty()) {
                foreach($order->groupOrders as $subOrder) {
                    $view = $this->orderRepository->printItemByType($subOrder->id, $subOrder, $type);
                }
            }
        } else {
            $view = $this->orderRepository->printItemByType($orderId, $order, $type);
        }

        return $this->sendResponse(compact('view'), null);
    }

    public function printMultiple(Request $request, $type) {
        $workspaceId = $request->get('workspace_id');
        $cond = [];
        $contents = [];
        $data = [];

        if($type == 'sticker') {
            $cond = [
                'field' => 'date_time',
                'cond' => '>=',
                'value' => now()
            ];
        }

        $orders = $this->orderRepository->getListOrders($request, $workspaceId, true, $cond);

        if(!empty($orders)) {
            $flag = 'timeslot';

            if($type == 'sticker') {
                $printSettingIdentical = SettingPrint::where('workspace_id', $workspaceId)
                    ->where('type', SettingPrint::TYPE_STICKER)
                    ->where('type_id', SettingPrint::IDENTICAL_PRODUCTS)
                    ->first();

                if(!empty($printSettingIdentical)) {
                    $flag = 'identical';
                }
            }

            if($flag == 'timeslot') {
                $contentA4 = '';
                $count = count($orders);
                $stt = 0;

                foreach($orders as $order) {
                    $timezone = !empty($order->timezone) ? $order->timezone : 'UTC';
                    $currentContents = OrderHelper::processPrint($order, $type, $timezone);

                    if($type == 'sticker') {
                        if(!empty($currentContents)) {
                            foreach($currentContents as $contentKey => $content) {
                                if ($content['type'] == 'image') {
                                    $currentContents[$contentKey]['url'] = \Storage::url($content['path']);
                                }
                            }

                            if($type != 'a4') {
                                $dataItem = OrderHelper::prepareOrderJobData($order, $type, $currentContents);
                                $data = array_merge($data, $dataItem);
                            }
                        }

                        $contents = array_merge($contents, $currentContents);
                    } else {
                        $stt++;

                        if(!empty($currentContents['order']) && !empty($currentContents['order']->print_products)) {
                            $contentA4 .= view('manager.orders.prints.'.$type, $currentContents)->render();

                            if($count > 1 && $stt < $count) {
                                $contentA4 .= '<div class="pagebreak"></div>';
                            }
                        }
                    }
                }

                if($type == 'a4') {
                    return $this->sendResponse(['view' => $contentA4], null);
                }
            } else {
                $triggerPrint = OrderHelper::triggerProcessPrintStickerIdentical($orders, $data, $type, $contents);
                $contents = $triggerPrint['contents'];
                $data = $triggerPrint['data'];
            }
        }

        $view = view('layouts.partials.print.content', compact('contents', 'type'))->render();

        OrderHelper::createJobAndCopyPrint($data, false, $type == 'sticker');

        return $this->sendResponse(compact('view'), null);
    }

    public function triggerConnectors($id) {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError(trans('order.trigger_connector_failed'));
        }

        // Start background process..
        dispatch(new \App\Jobs\PushOrderToConnectors($order->id, \App\Jobs\PushOrderToConnectors::TRIGGER_TYPE_MANUAL));

        return $this->sendResponse(null, trans('order.trigger_connector_success'));
    }

    /**
     * @return mixed|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }

    /**
     * Creating a new sms.
     *
     * @return Response
     */
    public function sendSms(Request $request)
    {
        $input = $request->all();
        $input['sent-at'] = Carbon::now();
        $sms = $this->smsRepository->create($input);
        $order = $this->orderRepository->findWithoutFail($sms->foreign_id);

        if (empty($order) || empty($order->contact_id)) {
            return $this->sendError(trans('order.contact_notfound'));
        }

        $contact = $this->contactRepository->findWithoutFail($order->contact_id);

        if (empty($contact)) {
            return $this->sendError(trans('order.contact_notfound'));
        }

        // send sms
        dispatch(new \App\Jobs\SendSms($sms, [$contact->phone]));

        return $this->sendResponse($sms, trans('order.sms_added_successfully'));
    }
}
