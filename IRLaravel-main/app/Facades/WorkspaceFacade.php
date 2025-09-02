<?php

namespace App\Facades;

use App\Helpers\WorkspaceHelper;

class WorkspaceFacade
{
    protected static function getFacadeAccessor()
    {
        return WorkspaceHelper::class;
    }
}