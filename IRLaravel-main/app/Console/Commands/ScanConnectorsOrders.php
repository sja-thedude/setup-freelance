<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SettingPrint;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanConnectorsOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'connectors:scan:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans orders for not yet pushed orders to connectors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $intervalHour = (int) config('connectors.global.order_scan_interval_hours');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // DEBUG: Simulate different date/time (DISABLE BEFORE COMMITTING!)
        if(false) {
            $date = '2022-08-02';
            $time = '11:00:00';
        }

        Order::select('orders.*')
            ->leftJoin('order_references', 'orders.id', '=', 'order_references.local_id')

            // Make sure we have atleast one connector for this workspace
            ->where(\DB::raw('0'), '<', function($query) {
                $query->select(\DB::raw('COUNT(setting_connectors.id)'))
                    ->from('setting_connectors')
                    ->where('orders.workspace_id', '=', \DB::raw('setting_connectors.workspace_id'));
            })

            // Order should be made for today
            ->where('orders.date', $date)

            // Calculate interval before printing
            ->where(\DB::raw('DATE_SUB(orders.time, INTERVAL '.$intervalHour.' HOUR)'), '<=', $time)

            // Make sure order isn't synced completely
            ->whereNull('order_references.completely_synced_at')

            // Order wasn't scheduled before..
            ->whereNull('order_references.auto_triggered_at')
            ->whereNull('order_references.auto_scheduled_at')
            ->whereNull('order_references.manually_triggered_at')

            ->where(function($query) use ($date) {
                // Orders should be from a different date then it was initial created
                // this helps prevent double prints
                $query = $query
                    // ORDERS OF TODAY WILL BE TRIGGERED DIRECTLY..
                    // WE WON'T SCHEDULE THEM TO PREVENT DOUBLES
                    ->whereNotBetween('orders.created_at', [$date . ' 00:00:00', $date . ' 23:59:59'])

                    // GROUP ORDERS WILL BE TRIGGERED TODAY
                    ->orWhere(function($query) {
                        $query = $query->whereNotNull('group_id')
                            ->whereNull('parent_id');

                        // @todo later we can optimize this part in such way that we only trigger orders that are cut-off
                        // But because of timezone calculation we are just going to create a job and check it there..
                    });
            })

            // Process in chunks
            ->chunk(50, function($orders) {
                /** @var Order $order */
                foreach($orders as $order) {
                    dispatch(new \App\Jobs\PushOrderToConnectors($order->id, \App\Jobs\PushOrderToConnectors::TRIGGER_TYPE_SCHEDULED));
                }
            });
    }
}
