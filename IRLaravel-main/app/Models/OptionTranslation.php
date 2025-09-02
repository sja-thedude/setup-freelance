<?php

namespace App\Models;

class OptionTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'optie_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'opties_id',
        'locale',
        'name',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }
}
