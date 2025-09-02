<?php

namespace App\Jobs;

use App\Models\SettingTimeslotDetail;
use App\Repositories\SettingOpenHourRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TriggerGenerateSixMonthTimeSlots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $workspaceId;
    protected $deleteConds;
    protected $relatedOrders;
    protected $addedOpenTimeslot;
    protected $settingTimeslot;

    /**
     * Create a new job instance.
     *
     * @param $data
     * @param $deleteConds
     */
    public function __construct(
        $workspaceId,
        $deleteConds = [],
        $addedOpenTimeslot = [],
        $settingTimeslot = null
    ){
        $this->workspaceId = $workspaceId;
        $this->deleteConds = $deleteConds;
        $this->addedOpenTimeslot = $addedOpenTimeslot;
        $this->settingTimeslot = $settingTimeslot;
    }

    /**
     * Execute the job.
     *
     * @param SettingOpenHourRepository $settingOpenHourRepository
     * @return void
     * @throws \Exception
     */
    public function handle(SettingOpenHourRepository $settingOpenHourRepository)
    {
        if(!empty($this->deleteConds)) {
            foreach ($this->deleteConds as $cond) {
                $model = new SettingTimeslotDetail();

                if(!empty($cond)) {
                    foreach ($cond as $condDetail) {
                        if($condDetail['cond'] == 'whereIn') {
                            $model = $model->whereIn($condDetail['field'], $condDetail['value']);
                        } else {
                            $model = $model->where($condDetail['field'], $condDetail['cond'], $condDetail['value']);
                        }
                    }
                }

                $model->delete();
            }
        }

        if(!empty($this->workspaceId)) {
            $settingOpenHourRepository->generateTimeSlotSixMonths(
                $this->workspaceId,
                $this->addedOpenTimeslot,
                $this->settingTimeslot
            );
        }
    }
}
