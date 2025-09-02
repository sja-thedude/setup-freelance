<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoPrintOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $order;
    private $fieldType;
    
    public function __construct(Order $order, $fieldType)
    {
        $this->order = $order;
        $this->fieldType = $fieldType;
    }
    
    public function handle() {
        \App\Facades\Order::autoPrintOrder([$this->order->id], [$this->fieldType['type']], true);
    }
}
