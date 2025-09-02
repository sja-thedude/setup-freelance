<?php

namespace App\Console\Commands;

use App\Jobs\ExportOrdersJob;
use App\Models\Workspace;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Log;

class OrderExportCommand extends Command
{
    protected $signature = 'order:export {workspaceId} {beginDate} {endDate}';
    
    protected $description = 'Command description';
    
    protected $orderRepository;
    
    /**
     * @param  OrderRepository  $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }
    
    public function handle(Request $request)
    {
        $beginDate = Carbon::parse($this->argument('beginDate'))->startOfDay();
        $endDate = Carbon::parse($this->argument('endDate'))->endOfDay();
        
        $workspaceId = $this->argument('workspaceId');
        $workspace = Workspace::find($workspaceId);
        
        $request->merge([
            'range_start_date' => $beginDate,
            'range_end_date' => $endDate
        ]);
        $orders = $this->orderRepository->getOrderListForExport($request, $workspaceId);
        $customers = $this->orderRepository->getCustomersListForExport($workspaceId, $beginDate, $endDate, 'UTC');
        dispatch_now(new ExportOrdersJob($orders, $customers,$workspace));
    }
}
