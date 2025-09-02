<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReference extends AppModel
{
    const STATUS_NONE = '';
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_DANGER = 'danger';

    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'auto_triggered_at',
        'auto_scheduled_at',
        'manually_triggered_at',
        'order_synced_at',
        'payment_synced_at',
        'completely_synced_at',
        'failed_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public $table = 'order_references';

    public $fillable = [
        'workspace_id',
        'local_id',
        'provider',
        'remote_id',
        'auto_triggered_at',
        'auto_scheduled_at',
        'manually_triggered_at',
        'order_synced_at',
        'payment_synced_at',
        'completely_synced_at',
        'failed_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'workspace_id' => 'integer',
        'local_id' => 'integer',
        'provider' => 'string',
        'remote_id' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
        'local_id' => 'required|integer',
        'provider' => 'required|string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'local_id');
    }

    public function getStatus($orderDatetime) {
        // Pushing order succeeded
        if(!empty($this->completely_synced_at)) {
            return self::STATUS_SUCCESS;
        }

        // Pushing order failed
        if(
            !empty($this->failed_at)
            && (
                !empty($this->auto_triggered_at)
                || !empty($this->auto_scheduled_at)
                || !empty($this->manually_triggered_at)
            )
        ) {
            return self::STATUS_DANGER;
        }

        $now = Carbon::now();
        $orderDateTime = Carbon::parse($orderDatetime);
        $diff = $now->diffInMinutes($orderDateTime);

        // We should have printed or at least triggered pushing order if we are 4 hours before order
        // Give 10 minutes margin because else we show during planning already a warning..
        if(
            $diff <= ((4 * 60) + 10)
            && $now < $orderDateTime
            && (
                !empty($this->auto_triggered_at)
                || !empty($this->auto_scheduled_at)
                || !empty($this->manually_triggered_at)
            )
        ) {
            return self::STATUS_WARNING;
        }

        // 10 Minutes before time still not pushed DANGER!
        if(
            ($diff <= 10 || $now >= $orderDateTime)
            && (
                !empty($this->auto_triggered_at)
                || !empty($this->auto_scheduled_at)
                || !empty($this->manually_triggered_at)
            )
        ) {
            return self::STATUS_DANGER;
        }

        return self::STATUS_NONE;
    }
}
