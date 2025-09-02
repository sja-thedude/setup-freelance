<?php

namespace App\Modules\ContentManager\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TermTranslation
 */
class TermTranslation extends Model
{

    protected $table = 'term_translations';

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

}
