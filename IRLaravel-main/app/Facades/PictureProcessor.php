<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class PictureProcessor
 * @package App\Facades
 */
class PictureProcessor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Helpers\PictureProcessor';
    }
}