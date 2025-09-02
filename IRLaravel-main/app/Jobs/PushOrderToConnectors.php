<?php

namespace App\Jobs;

use App\Jobs\PushOrderToConnectors\HendrickxKassas;
use App\Models\Order;
use App\Models\SettingConnector;
use App\Models\Workspace;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * Class PushOrderToConnectors
 * @package App\Jobs
 */
class PushOrderToConnectors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // When triggered automatically during order process
    const TRIGGER_TYPE_AUTO = 'auto';

    // When scheduled by cronjob
    const TRIGGER_TYPE_SCHEDULED = 'scheduled';

    // When triggered by manager or by cli manually
    const TRIGGER_TYPE_MANUAL = 'manual';

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var null
     */
    protected $triggeredType = null;

    /**
     * @var bool Dispatch directly (dispatchSync=true) or dispatch thru queue (dispatchSync=false)
     */
    protected $dispatchSync = false;

    /**
     * Create a new job instance.
     *
     * @param $orderId
     */
    public function __construct(
        $orderId,
        $triggeredType,
        $dispatchSync = false
    ) {
        $this->orderId = $orderId;
        $this->triggeredType = $triggeredType;
        $this->dispatchSync = $dispatchSync;
    }

    /**
     * Execute the job
     *
     * @param OrderRepository $orderRepository
     * @return void
     */
    public function handle(
        OrderRepository $orderRepository
    ) {
        /** @var Order $order */
        $order = $orderRepository->find((int) $this->orderId);
        if (empty($order)) {
            Log::error('PushOrderToConnectors: could not find order ID ' . ((int) $this->orderId));
            return;
        }

        /** @var Workspace|null|false $workspace */
        $workspace = $order->workspace;
        if(empty($workspace)) {
            Log::error('PushOrderToConnectors: could not find order (' . ((int) $this->orderId) . ') -> workspace');
            return;
        }

        // GROUP ORDER HANDLING IGNORE ORDER NOT READY YET..
        if(!empty($order->group_id) && $order->is_cut_of_time) {
            Log::error('PushOrderToConnectors: Not past cut of time so ignore (' . ((int) $this->orderId) . ') -> is_cut_of_time');
            return; // Skip order we should not send it yet..
        }

        // Loop thru connectors and trigger processing..
        $workspace->connectors()->chunk(50, function ($connectors) use ($order) {
            /** @var SettingConnector[] $connectors */
            foreach($connectors as $connector) {
                switch($connector->provider) {
                    case SettingConnector::PROVIDER_HENDRICKX_KASSAS:
                        $this->dispatchJob(new HendrickxKassas($connector->id, $order->id, $this->triggeredType));
                        break;
                }
            }
        });
    }

    /**
     * @param $job
     * @return void
     */
    protected function dispatchJob($job) {
        if(empty($this->dispatchSync)) {
            dispatch($job);
        }
        else {
            dispatch_now($job);
        }
    }
}