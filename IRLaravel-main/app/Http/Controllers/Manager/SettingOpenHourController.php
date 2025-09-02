<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\UpdateSettingOpenHourRequest;
use App\Repositories\SettingConnectorRepository;
use App\Repositories\SettingOpenHourRepository;
use App\Repositories\SettingExceptHourRepository;
use Illuminate\Http\Request;

class SettingOpenHourController extends BaseController
{
    private $settingOpenHourRepository;
    private $settingExceptHourRepository;

    public function __construct(
        SettingOpenHourRepository $settingOpenHourRepo,
        SettingExceptHourRepository $settingExceptHourRepo,
        SettingConnectorRepository $settingConnectorRepo
    ) {
        parent::__construct();

        $this->settingOpenHourRepository = $settingOpenHourRepo;
        $this->settingExceptHourRepository = $settingExceptHourRepo;
        $this->settingConnectorRepository = $settingConnectorRepo;
    }

    /**
     * @param UpdateSettingOpenHourRequest $request
     * @param int $workspaceId
     * @param int $settingId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateSettingOpenHourRequest $request, int $workspaceId, int $settingId)
    {
        $input = $request->all();
        $type = $input['type'];
        unset($input['type']);
        $setting = $this->settingOpenHourRepository->findWithoutFail($settingId);
        $data = null;

        $connectorsList = $this->getConnectorsList();

        $settingOpenHourReferences = null;
        if(!empty($connectorsList)) {
            $settingOpenHourReferences = $this->settingOpenHourRepository->getSettingOpenhourReferencesByWorkspace($this->tmpWorkspace->id);
        }

        $this->settingOpenHourRepository->updateOrCreateOpenHourReferences($input, $this->tmpWorkspace->id, $connectorsList);

        if($type == 'open-hours') {
            $this->settingOpenHourRepository->changeActiveSetting($setting);
        } else {
            $this->settingOpenHourRepository->updateTimeSlots($setting, $input);
            $dayInWeek = [1,2,3,4,5,6,0];
            $data['view'] = view('manager.settings.partials.open_hours.setting_form', [
                'dayInWeek' => $dayInWeek,
                'workspaceId' => $workspaceId,
                'settingOpenHour' => $setting->refresh(),
                'connectorsList'=> $connectorsList,
                'settingOpenHourReferences' => $settingOpenHourReferences,
            ])->render();
        }

        return $this->sendResponse($data, trans('setting_open_hour.updated_success'));
    }

    public function storeHolidayException(Request $request, int $workspaceId) {
        $input = $request->all();
        $this->settingExceptHourRepository->storeHolidayException($workspaceId, $input);

        return $this->sendResponse(null, trans('setting_open_hour.updated_holiday_success'));
    }

    /**
     * @return mixed|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function getConnectorsList() {
        $isShowConnectors = $this->tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first();

        if(empty($isShowConnectors) || !$isShowConnectors->active) {
            return null;
        }

        return $this->settingConnectorRepository
            ->getLists($this->tmpWorkspace->id, false);
    }
}
