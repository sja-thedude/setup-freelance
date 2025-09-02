<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'active',
        'name',
        'description',
        'permission'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Role::class;
    }
}
