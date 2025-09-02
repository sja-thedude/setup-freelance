<?php

namespace App\Traits;

trait AppTrait
{

    protected static $instances;

    /**
     * Creating a Singleton base class in PHP
     * @link https://stackoverflow.com/a/3972731
     * @return \App\Models\AppModel
     */
    public static function getInstance() {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }

    /**
     * static enums
     * @access static
     *
     * @param mixed $value
     * @param array $options
     * @param string $default
     * @return string|array
     */
    public static function enum($value, $options, $default = '') {
        if ($value !== null) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }
            return $default;
        }
        return $options;
    }

}