<?php

use Illuminate\Database\Seeder;
use App\Models\NotificationDevice;

class NotificationRemoveDuplicateDeviceIdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotificationDevice::select('id', DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('device_id')
            ->havingRaw('COUNT(id) > 1')
            ->orderBy('id', 'DESC')
            ->chunk(100, function($users) {
                foreach ($users as $user) {
                    NotificationDevice::whereIn('id', explode(',', $user->ids))
                        ->where('id', '!=', $user->id)
                        ->delete();
                }
            });
    }
}
