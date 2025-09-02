<?php

namespace App\Repositories;

use App\Models\PrinterGroupWorkspace;

class PrinterGroupWorkspaceRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'printer_group_id',
        'workspace_id'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return PrinterGroupWorkspace::class;
    }
}
