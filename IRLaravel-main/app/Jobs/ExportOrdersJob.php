<?php

namespace App\Jobs;

use App\Excel\Export\OrderOptionItemsExport;
use App\Excel\Export\CustomersExport;
use App\Excel\Export\OrderItemsExport;
use App\Excel\Export\OrdersExport;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $orders;
    protected $workspace;
    protected $customers;
    
    public function __construct($orders, $customers,$workspace)
    {
        $this->orders = $orders;
        $this->customers = $customers;
        $this->workspace = $workspace;
    }
    
    public function handle(Request $request)
    {
        \Log::info('Handling job with data', [
            'customers' => $this->customers,
        ]);
        
        info('job', [$this->customers->count()]);
		Excel::store(new OrdersExport($this->orders), "{$this->workspace->name}/Orders.csv", null, \Maatwebsite\Excel\Excel::CSV);
		Excel::store(new CustomersExport($this->customers), "{$this->workspace->name}/Customers.csv", null, \Maatwebsite\Excel\Excel::CSV);
		Excel::store(new OrderItemsExport($this->orders), "{$this->workspace->name}/OrderItems.csv", null, \Maatwebsite\Excel\Excel::CSV);
		Excel::store(new OrderOptionItemsExport($this->orders), "{$this->workspace->name}/OrderOptionItems.csv", null, \Maatwebsite\Excel\Excel::CSV);
	}
}
