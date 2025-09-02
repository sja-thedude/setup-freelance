<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use Illuminate\Console\Command;

class GenerateOrderAccessKeyWorkspace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspace:generate_order_access_key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Workspace::where('order_access_key', null)
            ->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    $order->order_access_key = strtoupper(\Webpatser\Uuid\Uuid::generate()->uuid_ordered);
                    $order->save();
                }
            });
    }
}
