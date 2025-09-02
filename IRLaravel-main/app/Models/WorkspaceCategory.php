<?php

namespace App\Models;

class WorkspaceCategory extends AppModel
{
    public $table = 'workspace_category';
    
    public $timestamps = false;

    public $fillable = [
        'restaurant_category_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @param $workspace
     * @param $input
     */
    public static function syncCategories($workspace, $input){
        $datas = [];
        if(!empty($input)) {
            foreach ($input as $id) {
                $datas[$id] = array(
                    'workspace_id' => $workspace->id,
                    'restaurant_category_id' => (int) $id
                );
            }
        }
        $workspace->workspaceCategories()->sync($datas);
    }
}
