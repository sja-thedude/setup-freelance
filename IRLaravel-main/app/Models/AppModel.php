<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{
    use Filterable;

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

    public static function getTableName() {
        return with(new static)->getTable();
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

    /**
     * const
     */
    const STATUS_TEMP = -1;

    /**
     * const
     */
    const STATUS_DELETE = 0;

    /**
     * const
     */
    const STATUS_ENABLE = 1;

    /**
     * const
     */
    const STATUS_DISABLE = 2;

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return string
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_TEMP => trans('strings.temporary'),
            self::STATUS_DELETE => trans('strings.delete'),
            self::STATUS_ENABLE => trans('strings.enable'),
            self::STATUS_DISABLE => trans('strings.disable'),
        );

        return self::enum($value, $options);
    }

    const IS_NO = 0;
    const IS_YES = 1;
    const ORDER_NONE = -1;
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return array|string|null
     */
    public static function by_orders($value = null) {
        $options = array(
            self::ORDER_ASC => trans('strings.ascending'),
            self::ORDER_DESC => trans('strings.descending'),
        );

        return self::enum($value, $options);
    }

    public $summary_fields = [];

    /**
     * @return array
     */
    public function getSummaryInfo() {
        if (empty($this)) {
            return null;
        }

        $arrInfo = $this->toArray();

        if (empty($this->summary_fields)) {
            return $arrInfo;
        }

        $data = [];

        foreach ($arrInfo as $field => $value) {
            if (in_array($field, $this->summary_fields)) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getFullInfo() {
        return [
            'id' => $this->id
        ];
    }

    /**
     * Fire events when create, update roles
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $arrOrderBy
     * @param array $whiteList
     * @return Model
     */
    public function appendOrderBy($model, $arrOrderBy, $whiteList = []) {
        $orderByDirections = ['ASC', 'DESC'];

        foreach ($arrOrderBy as $item) {
            if (is_array($item) && isset($item['property']) && isset($item['direction'])) {
                $column = $item['property'];
                $direction = strtoupper($item['direction']);

                if (in_array($column, $whiteList) && in_array($direction, $orderByDirections)) {
                    $model = $model->orderBy($column, $direction);
                }
            }
        }

        return $model;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where($this->getTable() . '.active', static::IS_YES);
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId) {
        return $query->where('user_id', $userId);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param array $attributes
     * @param array $whitelist
     * @return array
     */
    public function castAttributes($attributes, $whitelist = null) {
        // Validate whitelist array
        if ($whitelist !== null && !is_array($whitelist)) {
            return [];
        }

        $data = [];

        foreach ($attributes as $key => $value) {
            if (!in_array($key, $whitelist)) {
                continue;
            }

            // Cast data
            $data[$key] = $this->castAttribute($key, $value);
        }

        return $data;
    }

    /**
     * Get data after convert
     *
     * @param string|null $timezone Timezone string
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getData($timezone = null)
    {
        // Change datetime fields with timezone
        $timezone = (!empty($timezone)) ? $timezone : config('app.timezone');

        foreach ($this->getDates() as $dateField) {
            if (!empty($this->getAttribute($dateField))) {
                $field = $this->getAttribute($dateField);

                $this->setAttribute($dateField, $field->setTimezone($timezone));
            }
        }

        return $this;
    }

}
