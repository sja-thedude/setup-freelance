<?php

namespace App\Http\Controllers\Manager;

use App\Repositories\SettingPrintRepository;
use Illuminate\Http\Request;

class SettingPrintController extends BaseController
{
    /** @var  SettingPrintRepository */
    private $settingPrintRepository;

    /**
     * SettingPrintController constructor.
     * @param SettingPrintRepository $settingPrintRepo
     */
    public function __construct(SettingPrintRepository $settingPrintRepo)
    {
        parent::__construct();

        $this->settingPrintRepository = $settingPrintRepo;
    }

    /**
     * @param int $workspaceId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreate(int $workspaceId, Request $request)
    {
        $input = $request->all();
        $input['workspace_id'] = $this->tmpWorkspace->id;
        
        $this->settingPrintRepository->updateOrCreatePrint($input);

        return $this->sendResponse([], trans('setting.more.updated_confirm'));
    }
}
