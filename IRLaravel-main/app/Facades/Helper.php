<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Class Helper
 * @package App\Facades
 */
class Helper extends Facade{
    protected static function getFacadeAccessor() { return 'App\Helpers\Helper'; }
}