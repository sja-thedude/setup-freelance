<?php

namespace App\Models;

class GroupRestaurantWorkspace extends AppModel
{
    protected $fillable = [
        'group_restaurant_id',
        'workspace_id'
    ];

    protected $table = 'group_restaurant_workspace';

    public $timestamps = false;

    public static function syncWorkspaces($groupRestaurant, $input)
    {
        $datas = [];
        if (!empty($input)) {
            foreach ($input as $id) {
                $datas[$id] = array(
                    'group_restaurant_id' => $groupRestaurant->id,
                    'workspace_id' => (int)$id
                );
            }
        }

        $groupRestaurant->groupRestaurantWorkspaces()->sync($datas);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function groupRestaurant()
    {
        return $this->belongsTo(GroupRestaurant::class, 'group_restaurant_id');
    }
}