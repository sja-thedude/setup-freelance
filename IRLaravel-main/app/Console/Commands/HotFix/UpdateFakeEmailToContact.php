<?php

namespace App\Console\Commands\HotFix;

use Illuminate\Console\Command;

class UpdateFakeEmailToContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:update_fake_email';

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
        \App\Models\Contact::whereNull('phone')
            ->whereRaw("email REGEXP '^[a-zA-Z0-9]+_[0-9]{13}@[a-zA-Z0-9.-]+$'")
            ->update(['fake_email' => true]);
    }
}
