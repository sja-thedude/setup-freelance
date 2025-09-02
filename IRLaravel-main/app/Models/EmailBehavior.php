<?php

namespace App\Models;

class EmailBehavior extends AppModel
{
    public $table = 'email_behaviors';

    public $fillable = [
        'created_at',
        'updated_at',
        'action',
        'email',
        'workspace_id',
        'group_restaurant_id',
        'origin'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'action' => 'string',
        'email' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'action' => 'required',
        'email' => 'required',
    ];

    const ACTION_RESET_PASSWORD = 'reset_password';

    const ORIGIN_LARAVEL = 'laravel';
    const ORIGIN_NEXT = 'next';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * Make a behavior
     *
     * @param string $action
     * @param string $email
     * @param int|null $workspace_id
     * @return EmailBehavior
     * @throws \Exception
     */
    public static function makeBehavior(
        string $action,
        string $email,
        int $workspace_id = null,
        int $group_restaurant_id = null,
        string $origin = self::ORIGIN_LARAVEL
    ) {
        $exist = static::where('email', $email)->first();

        // Delete if exist
        if (!empty($exist)) {
            $exist->delete();
        }

        return static::create([
            'action' => $action,
            'email' => $email,
            'workspace_id' => $workspace_id,
            'group_restaurant_id' => $group_restaurant_id,
            'origin' => $origin
        ]);
    }

    /**
     * Get a behavior
     *
     * @param string $action
     * @param string $email
     * @return EmailBehavior
     * @throws \Exception
     */
    public static function getBehavior(string $action, string $email)
    {
        $behavior = static::where([
            'action' => $action,
            'email' => $email,
        ])->first();

        return $behavior;
    }

}
