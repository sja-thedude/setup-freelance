<?php

namespace App\Console\Commands;

use App\Models\NotificationPlan;
use App\Repositories\NotificationRepository;
use Illuminate\Console\Command;

class PushOrderToConnectors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'connectors:push:order {id} {--dispatchSync=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push order to configured connectors';

    /**
     * Create a new command instance.
     *
     * @param NotificationRepository $notificationRepo
     */
    public function __construct(NotificationRepository $notificationRepo)
    {
        parent::__construct();

        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderId = (int) $this->argument('id');

        $dispatchSync = false;
        if(
            $this->hasOption('dispatchSync')
            && !empty($this->option('dispatchSync'))
        ) {
            $dispatchSync = true;
        }

        dispatch_now(new \App\Jobs\PushOrderToConnectors($orderId, \App\Jobs\PushOrderToConnectors::TRIGGER_TYPE_MANUAL, $dispatchSync));
    }
}
