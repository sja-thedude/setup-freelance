<?php

namespace App\Modules\ContentManager\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TermMeta
 * @package App\Modules\ContentManager\Models
 */
class TermMeta extends Model
{
    /**
     * @var string
     */
    protected $table = 'term_meta';
    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = array(
        'term_id',
        'meta_key',
        'meta_value'
    );
}
