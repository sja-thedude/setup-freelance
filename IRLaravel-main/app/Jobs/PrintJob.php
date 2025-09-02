<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\SettingPrint;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class PrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $now = null;
    
    public function __construct($now = null)
    {
        if(is_null($now)) {
            $now = date('Y-m-d H:i:s');
        }

        $this->now = $now;

        if(config('print.debug') == 1) {
            Log::info('[PrintJob] Constructor called with $now: ' . $now);
        }
    }
    
    public function handle()
    {
        Log::info('PrintJob', ['now' => $this->now]);

        $fieldTypes = [
            ['field' => 'auto_print_sticker', 'type' => SettingPrint::TYPE_STICKER],
            ['field' => 'auto_print_werkbon', 'type' => SettingPrint::TYPE_WERKBON],
            ['field' => 'auto_print_kassabon', 'type' => SettingPrint::TYPE_KASSABON]
        ];
        
        foreach ($fieldTypes as $fieldType) {
            if(config('print.debug') == 1) {
                Log::info('[PrintJob] Calling callAutoPrint for field: ' . $fieldType['field'] . ', type:' . $fieldType['type']);
            }

            $this->callAutoPrint($fieldType);
        }

        if(config('print.debug') == 1) {
            Log::info('[PrintJob] Handle method finished.');
        }
    }
    
    public function callAutoPrint($fieldType)
    {
        $now = $this->now;

        $orders = Order::where(function($query) use ($fieldType) {
            $type = $fieldType['type'];
            
            if($type == SettingPrint::TYPE_KASSABON) {
                $query->where(function($subQuery) {
                    // individual
                    $subQuery->whereNull('group_id')->whereNotIn('type', [Order::TYPE_IN_HOUSE, Order::TYPE_SELF_ORDERING]);
                })->orWhere(function($subQuery) {
                    // in house (sub order)
                    $subQuery->whereNull('group_id')->where('type', Order::TYPE_IN_HOUSE)->whereNotNull('parent_id');
                })->orWhere(function($subQuery) {
                    // self ordering (parent order)
                    $subQuery->whereNull('group_id')->where('type', Order::TYPE_SELF_ORDERING)->whereNull('parent_id');
                })->orWhere(function($subQuery) {
                    // group order
                    $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                });
            } else {
                $query->where(function($subQuery) {
                    // individual
                    $subQuery->whereNull('group_id')->whereNotIn('type', [Order::TYPE_IN_HOUSE, Order::TYPE_SELF_ORDERING]);
                })->orWhere(function($subQuery) {
                    // in house & self ordering (parent order)
                    $subQuery->whereNull('group_id')->whereIn('type', [Order::TYPE_IN_HOUSE, Order::TYPE_SELF_ORDERING])->whereNull('parent_id');
                })->orWhere(function($subQuery) {
                    // group order
                    $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                });
            }
        })
            ->where($fieldType['field'], false)
            ->where('run_crontab', true)
            ->where('date_time', '>=', Carbon::now()->subWeek())
            ->get();

        if(!$orders->isEmpty()) {
            $orderIds = $orders->pluck('id')->all();

            if(config('print.debug') == 1) {
                Log::info("[PrintJob::callAutoPrint] Found " . count($orderIds) . " order(s) for field: {$fieldType['field']}. Order IDs: " . implode(',', $orderIds));
            }

            \App\Facades\Order::autoPrintOrder($orderIds, [$fieldType['type']], true, $now);
        }
    }
}
