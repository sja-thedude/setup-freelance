<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use App\Repositories\SettingOpenHourRepository;
use Illuminate\Console\Command;

class GenerateSixMonthCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'time_slot:generate-six-months';
    protected $settingOpenHourRepository;

    public function __construct(SettingOpenHourRepository $settingOpenHourRepository)
    {
        parent::__construct();

        $this->settingOpenHourRepository = $settingOpenHourRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $workspaces = Workspace::all();

        if(!empty($workspaces)) {
            $workspaceIds = $workspaces->pluck('id')->all();

            foreach ($workspaceIds as $workspaceId) {
                dispatch(new \App\Jobs\TriggerGenerateSixMonthTimeSlots($workspaceId));
            }
        }
    }
}
