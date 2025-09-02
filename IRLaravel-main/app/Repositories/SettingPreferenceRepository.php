<?php

namespace App\Repositories;

use App\Models\SettingPreference;

class SettingPreferenceRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'takeout_min_time',
        'takeout_day_order',
        'delivery_min_time',
        'delivery_day_order',
        'mins_before_notify',
        'use_sms_whatsapp',
        'use_email',
        'receive_notify',
        'sound_notify',
        'opties_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingPreference::class;
    }

    /**
     * @param $input
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreatePreference($input) {
        $input['use_sms_whatsapp'] = !empty($input['use_sms_whatsapp']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;
        $input['use_email'] = !empty($input['use_email']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;
        $input['receive_notify'] = !empty($input['receive_notify']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;
        $input['sound_notify'] = !empty($input['sound_notify']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;
        $input['service_cost_set'] = !empty($input['service_cost_set']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;

        if(empty($input['service_cost_always_charge_disabled'])) {
            $input['service_cost_always_charge'] = !empty($input['service_cost_always_charge']) ? SettingPreference::ACTIVE : SettingPreference::INACTIVE;
        }

        $preference = $this->makeModel()->updateOrCreate([
            'workspace_id' => $input['workspace_id']
        ], $input);
        
        return $preference;
    }

    /**
     * @param $workspaceId
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function initSettingPreferenceForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [
                'workspace_id' => $workspaceId,
                'takeout_min_time' => config('settings.preferences.takeout_min_time'),
                'takeout_day_order' => config('settings.preferences.takeout_day_order'),
                'delivery_min_time' => config('settings.preferences.delivery_min_time'),
                'delivery_day_order' => config('settings.preferences.delivery_day_order'),
                'mins_before_notify' => config('settings.preferences.mins_before_notify'),
                'use_sms_whatsapp' => config('settings.preferences.use_sms_whatsapp'),
                'use_email' => config('settings.preferences.use_email'),
                'receive_notify' => config('settings.preferences.receive_notify'),
                'sound_notify' => config('settings.preferences.sound_notify'),
                'opties_id' => config('settings.preferences.opties_id'),
                'created_at' => $now,
                'updated_at' => $now
            ];

            if(!empty($data)) {
                SettingPreference::insert($data);
            }
        }

        return true;
    }
}
