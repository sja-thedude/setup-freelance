<?php

namespace App\Console\Commands;

use App\Jobs\PrintJob;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use App\Models\SettingPrint;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrintJobScan extends Command
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print_job_scan:order';

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
        $now = date('Y-m-d H:i:s');

        Log::info('PrintJobScan', ['now' => $now]);

        dispatch(new PrintJob($now))
            ->onQueue('prints');
    }
}
