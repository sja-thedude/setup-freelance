<?php

use Illuminate\Database\Seeder;

class SettingForWorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workspaces = \App\Models\Workspace::get();
        $settingGeneralRepository = new \App\Repositories\SettingGeneralRepository(app());
        $settingPreferenceRepository = new \App\Repositories\SettingPreferenceRepository(app());
        $settingPaymentRepository = new \App\Repositories\SettingPaymentRepository(app());
        $settingDeliveryConditionsRepository = new \App\Repositories\SettingDeliveryConditionsRepository(app());
        $settingPrintRepository = new \App\Repositories\SettingPrintRepository(app());
        
        if (!$workspaces->isEmpty()) {
            foreach ($workspaces as $workspace) {
                $settingGeneralRepository->initSettingGeneralForWorkspace($workspace->id);
                $settingPreferenceRepository->initSettingPreferenceForWorkspace($workspace->id);
                $settingPaymentRepository->initSettingPaymentForWorkspace($workspace->id);
                $settingDeliveryConditionsRepository->initSettingDeliveryConditionsForWorkspace($workspace->id);
                $settingPrintRepository->initSettingPrintForWorkspace($workspace->id);
            }
        }
    }
}
