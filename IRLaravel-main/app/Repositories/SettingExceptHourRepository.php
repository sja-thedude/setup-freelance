<?php

namespace App\Repositories;

use App\Models\SettingExceptHour;
use App\Helpers\Helper;

class SettingExceptHourRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'start_time',
        'end_time',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingExceptHour::class;
    }

    public function getSettingByWorkspace($workspaceId) {
        return $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();
    }

    public function storeHolidayException($workspaceId, $input) {
        $timezone = 'UTC';

        if(!empty($input['timezone'])) {
            $timezone = $input['timezone'];
            unset($input['timezone']);
        }

        if(!empty($input['holiday'])) {
            $data = $input['holiday'];
            $ids = [];

            foreach($data as $key => $item) {
                if(!empty($item['id'])) {
                    $ids[] = $item['id'];
                }

                unset($data[$key]['date_range']);

                $data[$key]['workspace_id'] = $workspaceId;
                //$data[$key]['start_time'] = Helper::convertDateTimeToUTC($data[$key]['start_time'], $timezone);
                //$data[$key]['end_time'] = Helper::convertDateTimeToUTC($data[$key]['end_time'], $timezone);
            }

            $this->makeModel()
                ->where('workspace_id', $workspaceId)
                ->whereNotIn('id', $ids)
                ->delete();

            if(!empty($data)) {
                foreach($data as $item) {
                    $id = $item['id'];
                    unset($item['id']);
                    SettingExceptHour::updateOrCreate(['id' => $id], $item);
                }
            }
        } else {
            $this->makeModel()
                ->where('workspace_id', $workspaceId)
                ->delete();
        }

        return true;
    }
}
