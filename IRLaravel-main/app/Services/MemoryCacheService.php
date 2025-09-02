<?php

namespace App\Services;

class MemoryCacheService {
    /**
     * @var null|MemoryCacheService
     */
    private static $instance = null;

    /**
     * Used to prevent us from retrieving the same thing over and over again.
     * @var array
     */
    protected $memoryCache = [];

    /**
     * Singleton
     */
    private function __construct() {
        // Isn't currently doing anything
    }

    /**
     * Singleton get instance method
     * @return MemoryCacheService|null
     */
    public static function getInstance()
    {
        if(empty(self::$instance)) {
            self::$instance = new MemoryCacheService();
        }

        return self::$instance;
    }

    /**
     * @param $namespace
     * @param $key
     * @param $alternative
     * @return mixed|null
     */
    public function get($namespace, $key, $alternative = null, $index = false) {
        if(!empty($index)) {
            $key .= '_index';
        }

        if(!isset($this->memoryCache[$namespace][$key])) {
            return $alternative;
        }

        return $this->memoryCache[$namespace][$key];
    }

    /**
     * @param $namespace
     * @param $key
     * @param $value
     * @return void
     */
    public function set($namespace, $key, $value, $index = false) {
        if(!isset($this->memoryCache[$namespace])) {
            $this->memoryCache[$namespace] = [];
        }

        $this->memoryCache[$namespace][$key] = $value;

        if(!empty($index)) {
            $this->memoryCache[$namespace][$key.'_index'] = true;
        }
    }

    /**
     * Example: $cacheId = $this->generateCacheId(get_class($this).'_'.__FUNCTION__, func_get_args());
     */
    public function generateCacheId($name, $params) {
        $name = str_replace('\\', '_', $name);
        $str = '';
        $i = 0;

        if (is_array($params) && count($params) != 0) {
            foreach ($params as $param) {
                $i++;

                if ($str == 1) {
                    $str .= var_export($param, true);
                } else {
                    $str .= '_' . var_export($param, true);
                }
            }
        }

        unset($i);

        if ($str != '') {
            $name = $name . '_' . sha1($str);
        }

        if (trim(substr($name, 50)) != '') {
            return substr($name, 0, 50) . '_' . sha1(substr($name, 50));
        }

        return $name;
    }
}