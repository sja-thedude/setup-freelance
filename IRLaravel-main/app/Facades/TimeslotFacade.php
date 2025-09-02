<?php

namespace App\Facades;

use App\Helpers\TimeslotHelper;
use Illuminate\Support\Facades\Facade;

/**
 * Class TimeslotFacade
 * @package App\Facades
 */
class TimeslotFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TimeslotHelper::class;
    }
}