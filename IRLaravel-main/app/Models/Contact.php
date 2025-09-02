<?php

namespace App\Models;

class Contact extends AppModel
{
    public $table = 'contacts';

    public $fillable = [
        'created_at',
        'updated_at',
        'locale',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'company_name',
        'content',
        'fake_email'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'company_name' => 'string',
        'content' => 'string',
        'fake_email' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:255',
        'first_name' => 'required|max:255',
        'last_name' => 'max:255',
        'email' => 'required|max:255|email',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function workspaces()
    {
        return $this->belongsToMany(\App\Models\Workspace::class, 'workspace_contact');
    }

}
