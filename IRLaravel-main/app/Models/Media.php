<?php

namespace App\Models;

use App\Facades\Helper;

class Media extends AppModel
{

    const MODEL_WORKSPACE = 'App\Models\Workspace';
    const MODEL_CATEGORY = 'App\Models\Category';
    const MODEL_GROUP_RESTAURANT = 'App\Models\GroupRestaurant';
    const AVATAR = 'avatar';
    const GALLERIES = 'galleries';
    const API_GALLERIES = 'api_galleries';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'foreign_id',
        'foreign_model',
        'foreign_type',
        'field_name',
        'file_name',
        'file_type',
        'file_size',
        'file_path',
        'full_path',
        'created_at',
        'updated_at',
        'active',
        'order'
    ];

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'file_path' => $this->file_path,
            'full_path' => Helper::getLinkFromDataSource($this->file_path, null, 'storage/'),
            'active'    => $this->active,
            'order'     => $this->order
        ];
    }

}
