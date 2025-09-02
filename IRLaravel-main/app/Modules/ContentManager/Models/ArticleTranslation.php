<?php

namespace App\Modules\ContentManager\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleTranslation
 */
class ArticleTranslation extends Model
{

    protected $table = 'post_translations';

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = [
        'post_content',
        'post_title',
        'post_excerpt',
        'post_name'
    ];

}
