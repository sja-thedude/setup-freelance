<?php

namespace App\Models;

class SettingConnector extends AppModel
{
    const PROVIDER_HENDRICKX_KASSAS = 'hendrickx-kassas';
    const PROVIDER_CUSTOM = 'custom';

    const DELIVERY_METHOD_TAKEOUT = 'takeout';
    const DELIVERY_METHOD_DELIVERY = 'delivery';
    const DELIVERY_METHOD_INHOUSE = 'inhouse';

    /**
     * @var null
     */
    protected $deliveryMethod = null;

    public $table = 'setting_connectors';
    
    public $timestamps = true;

    public $fillable = [
        'workspace_id',
        'provider',

        // Global
        'endpoint', // This will contain the endpoint for example http://1.2.3.4:4000
        'key', // For Hendrickx kassas we use this for the signature key
        'token', // For Hendrickx kassas we use this as encryption token
        'refresh_token', // For Custom provider we use this as refresh token

        // Overwrite takeout
        'takeout_endpoint',
        'takeout_key',
        'takeout_token',

        // Overwrite delivery
        'delivery_endpoint',
        'delivery_key',
        'delivery_token',

        // Overwrite inhouse
        'inhouse_endpoint',
        'inhouse_key',
        'inhouse_token',

        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'provider' => 'string',
        'endpoint' => 'encrypted',
        'key' => 'encrypted',
        'token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $model->takeout_endpoint = !empty($model->takeout_endpoint) ? $model->takeout_endpoint : '';
            $model->takeout_key = !empty($model->takeout_key) ? $model->takeout_key : '';
            $model->takeout_token = !empty($model->takeout_token) ? $model->takeout_token : '';

            $model->delivery_endpoint = !empty($model->delivery_endpoint) ? $model->delivery_endpoint : '';
            $model->delivery_key = !empty($model->delivery_key) ? $model->delivery_key : '';
            $model->delivery_token = !empty($model->delivery_token) ? $model->delivery_token : '';

            $model->inhouse_endpoint = !empty($model->inhouse_endpoint) ? $model->inhouse_endpoint : '';
            $model->inhouse_key = !empty($model->inhouse_key) ? $model->inhouse_key : '';
            $model->inhouse_token = !empty($model->inhouse_token) ? $model->inhouse_token : '';
        });
    }

    public function deliveryMethods() {
        return [
            self::DELIVERY_METHOD_TAKEOUT => trans('setting.connectors.manager.takeout'),
            self::DELIVERY_METHOD_DELIVERY => trans('setting.connectors.manager.delivery'),
            self::DELIVERY_METHOD_INHOUSE => trans('setting.connectors.manager.in_house'),
        ];
    }

    public function setDeliveryMethodByOrderType($orderType) {
        switch($orderType) {
            case Order::TYPE_TAKEOUT:
                $this->setDeliveryMethod(self::DELIVERY_METHOD_TAKEOUT);
                break;

            case Order::TYPE_DELIVERY:
                $this->setDeliveryMethod(self::DELIVERY_METHOD_DELIVERY);
                break;

            case Order::TYPE_IN_HOUSE:
                $this->setDeliveryMethod(self::DELIVERY_METHOD_INHOUSE);
                break;
        }

        return $this;
    }

    public function setDeliveryMethod($deliveryMethod) {
        if(!empty($this->deliveryMethods()[$deliveryMethod])) {
            $this->deliveryMethod = $deliveryMethod;
        }

        return $this;
    }

    public function getEndpointBasedOnDeliveryMethod() {
        $variableNameEndpoint = $this->deliveryMethod . '_endpoint';

        if(!empty($this->getAttribute($variableNameEndpoint))) {
            return $this->getAttribute($variableNameEndpoint);
        }

        return $this->endpoint;
    }

    public function getKeyBasedOnDeliveryMethod() {
        $variableNameKey = $this->deliveryMethod . '_key';

        if(!empty($this->getAttribute($variableNameKey))) {
            return $this->getAttribute($variableNameKey);
        }

        return $this->key;
    }

    public function getTokenBasedOnDeliveryMethod() {
        $variableNameToken = $this->deliveryMethod . '_token';

        if(!empty($this->getAttribute($variableNameToken))) {
            return $this->getAttribute($variableNameToken);
        }

        return $this->token;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @param null $value
     * @return array|string
     */
    public static function getProviders($value = null, $isUsed = null, $isUsedExcluseId = null, $workspaceId = null)
    {
        /**
         * !! IMPORTANT:
         * If you want to add a provider always add at the end.
         * Because export and import of products, product options will depend on this list and add them to the import,
         * export functionality.
         */

        $options = array(
            static::PROVIDER_HENDRICKX_KASSAS => trans('setting.connectors.providers.hendrickx_kassas'),
            static::PROVIDER_CUSTOM => trans('setting.connectors.providers.custom'),
        );

        // Filter providers..
        if(is_bool($isUsed)) {
            $providers = SettingConnector::select('provider')
                ->groupBy('provider')
                ->where('id', '!=', $isUsedExcluseId)
                ->where('workspace_id', $workspaceId)
                ->pluck('provider');

            if($isUsed === false) {
                foreach($providers as $providerKey) {
                    if(isset($options[$providerKey])) {
                        unset($options[$providerKey]);
                    }
                }
            }
        }

        return static::enum($value, $options);
    }
}
