<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SettingPrint;
use App\Models\Order;

class PrintBbcodeToText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print_bbcode_to:text {bbcode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helps with debugging sticker prints';

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
        $this->info("\n\n");

        $bbcode = $this->argument('bbcode');
        $this->info(str_replace(array_keys($this->mapBbCode()), array_values($this->mapBbCode()), $bbcode));
    }

    /**
     * @see \App\Services\Star\LineJob
     * @return string[]
     */
    protected function mapBbCode() {
        return [
            '[b]' => '',
            '[/b]' => '',
            '[\/b]' => '',
            '[center]' => '',
            '[/center]' => '',
            '[\/center]' => '',
            '[right]' => '',
            '[/right]' => '',
            '[\/right]' => '',
            '[pagebreak/]' => "\n\n\n\n",
            '[pagebreak\/]' => "\n\n\n\n",
            '[br/]' => "\n",
            '[br\/]' => "\n",
            '[feedpartialcut/]' => "\n\n",
            '[feedpartialcut\/]' => "\n\n",
            '[feedfullcut/]' => "\n\n",
            '[feedfullcut\/]' => "\n\n",
            '[textline]' => '',
            '[/textline]' => "\n",
            '[\/textline]' => "\n",
            '[space/]' => ' ',
            '[space\/]' => ' ',
            '[tab/]' => '   ',
            '[tab\/]' => '   ',
            '[h1]' => '',
            '[/h1]' => '',
            '[\/h1]' => '',
            '[/euro]' => '€',
            '[\/euro]' => '€',
            '[/seperator]' => '------------------------------------------------',
            '[\/seperator]' => '------------------------------------------------',
        ];
    }
}
