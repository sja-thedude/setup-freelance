<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_ERROR = 3;

    public $table = 'sms';

    public $fillable = [
        'workspace_id',
        'status',
        'message',
        'sent_at',
        'foreign_model',
        'foreign_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @param null $value
     * @return mixed
     */
    public static function statusOptions($value = null) {
        $options = array(
            static::STATUS_PENDING => trans('sms.to_do'),
            static::STATUS_SENT => trans('sms.done'),
            static::STATUS_ERROR => trans('sms.error')
        );

        return $options[$value];
    }
}
