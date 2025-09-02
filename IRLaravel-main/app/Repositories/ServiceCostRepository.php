<?php

namespace App\Repositories;

use App\Models\SettingPreference;
use App\Models\WorkspaceExtra;

class ServiceCostRepository extends AppBaseRepository
{
    public function model()
    {
        return SettingPreference::class;
    }
    /**
     * @param $workspaceId
     * @return mixed
     */
    public function serviceCostEnabled($workspaceId) {
        $workspaceExtraServiceCost = WorkspaceExtra::where('type', WorkspaceExtra::SERVICE_COST)
            ->where('workspace_id', $workspaceId)
            ->where('active', true)
            ->first();

        if(!empty($workspaceExtraServiceCost)) {
            $settingPreference = $this->findWhere(['workspace_id' => $workspaceId])->first();

            if(!empty($settingPreference) && !empty($settingPreference->service_cost_set)) {
                return $settingPreference;
            }
        }

        return null;
    }

    /**
     * @param $settingServiceCost
     * @param $orders
     * @return array
     */
    public function calculateServiceCostForCart($workspaceId, $price) {
        $cost = 0;

        if(!empty($workspaceId)) {
            $settingServiceCost = $this->serviceCostEnabled($workspaceId);

            if(!is_null($settingServiceCost)) {
                if(!empty($settingServiceCost->service_cost_always_charge) || $price < $settingServiceCost->service_cost_amount) {
                    $cost = $settingServiceCost->service_cost;
                }
            }
        }

        return $cost;
    }
}
