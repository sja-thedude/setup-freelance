<?php

namespace App\Http\Controllers\Manager;

use App\Helpers\Helper;
use App\Http\Requests\UpdateSettingTimeslotRequest;
use App\Repositories\SettingTimeslotRepository;
use App\Repositories\SettingOpenHourRepository;
use Illuminate\Http\Request;

class SettingTimeslotController extends BaseController
{
    /** @var  SettingTimeslotRepository */
    private $settingTimeslotRepository;
    private $settingOpenHourRepository;

    public function __construct(
        SettingTimeslotRepository $settingTimeslotRepo,
        SettingOpenHourRepository $settingOpenHourRepo
    ) {
        parent::__construct();

        $this->settingTimeslotRepository = $settingTimeslotRepo;
        $this->settingOpenHourRepository = $settingOpenHourRepo;
    }

    /**
     * @param Request $request
     * @param int $workspaceId
     * @param int $settingId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateSettingTimeslotRequest $request, int $workspaceId, int $settingId)
    {
        $input = $request->all();
        $setting = $this->settingTimeslotRepository->findWithoutFail($settingId);
        $this->settingTimeslotRepository->updateTimeSlots($setting, $input);

        return $this->sendResponse(null, trans('time_slot.updated_success'));
    }

    public function renderTimeSlotDetail(Request $request, int $workspaceId, int $settingId)
    {
        $date = $request->get('date', null);
        $settingOpenHourTimes = [];
        $settingTimeSlotDetails = [];
        $settingTimeSlot = $this->settingTimeslotRepository->findWithoutFail($settingId);
        $settingOpenHour = $this->settingOpenHourRepository->getByTimeSlotType($settingTimeSlot->type, $workspaceId);
        $dayOfWeek = -1;

        if(empty($settingTimeSlot)) {
            return $this->sendError(trans('common.not_found'));
        }

        if(!empty($date)) {
            $dateConvert = date(config('datetime.dateFormatServer'), strtotime(str_replace('/', '-', $date)));
            $dayOfWeek = \Carbon\Carbon::parse($dateConvert . ' 00:00:00')->dayOfWeek;

            if(!empty($settingOpenHour)) {
                $settingOpenHourTimes = $settingOpenHour->openTimeSlots()->where('day_number', $dayOfWeek)->get();
            }

            $timeSlotArrs = $timeSlotTmpArrs = [];

            if(!$settingOpenHourTimes->isEmpty()) {
                foreach($settingOpenHourTimes as $settingOpenHourTime) {
                    $timeSlotTmpArrs[] = [
                        'start_time' => $settingOpenHourTime->start_time,
                        'end_time' => $settingOpenHourTime->end_time
                    ];
                }

                $timeSlotArrs = Helper::splitTime($settingTimeSlot->interval_slot, $timeSlotTmpArrs);
            }

            if(!empty($timeSlotArrs)) {
                $settingTimeSlotDetails = $this->settingTimeslotRepository->initTimeSlotDetail(
                    $workspaceId,
                    $dateConvert,
                    $dayOfWeek,
                    $settingTimeSlot,
                    $timeSlotArrs
                );
            }
        }

        $view = view('manager.settings.partials.time_slots.form_time_slot_detail', [
            'dayOfWeek' => $dayOfWeek,
            'workspaceId' => $workspaceId,
            'settingOpenHour' => $settingOpenHour,
            'settingTimeSlot' => $settingTimeSlot,
            'settingTimeSlotDetails' => $settingTimeSlotDetails
        ])->render();

        return $this->sendResponse(compact('view', 'settingTimeSlot'), trans('common.success'));
    }

    public function updateTimeSlotDetail(Request $request, int $workspaceId, int $settingId)
    {
        $input = $request->all();
        $this->settingTimeslotRepository->updateTimeSlotDetail($input);

        return $this->sendResponse(null, trans('common.success'));
    }
}
