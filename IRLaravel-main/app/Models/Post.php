<?php

namespace App\Models;

use App\Modules\ContentManager\Models\Articles;
use App\Traits\AppTrait;
use Dimsav\Translatable\Translatable;

class Post extends Articles
{
    use Translatable, AppTrait;

    public $fillable = [
        'created_at',
        'updated_at',
        'post_author',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'post_password',
        'post_name',
        'post_parent',
        'guid',
        'menu_order',
        'menu_group',
        'post_type',
        'post_hit',
        'post_mime_type',
    ];

    public $translatedAttributes = [
        'post_name',
        'post_title',
        'post_content',
        'post_excerpt',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'post_name' => 'string',
        'post_title' => 'string',
        'post_content' => 'string',
        'post_excerpt' => 'string',
        'post_author' => 'integer',
        'post_status' => 'string',
        'comment_status' => 'string',
        'post_password' => 'string',
        'post_parent' => 'integer',
        'guid' => 'string',
        'menu_order' => 'integer',
        'menu_group' => 'string',
        'post_type' => 'string',
        'post_hit' => 'integer',
        'post_mime_type' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    const POST_TYPE_POST = 'post';
    const POST_TYPE_ABOUT = 'about';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'post_author');
    }

    const STATUS_PUBLISH = 'publish';
    const STATUS_DRAFT = 'draft';

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param string|null $value
     * @return array|string|null
     */
    public static function statuses($value = null) {
        $options = array(
            static::STATUS_PUBLISH => trans('strings.published'),
            static::STATUS_DRAFT => trans('strings.draft'),
        );
        return static::enum($value, $options);
    }

    /**
     * Get default selected align
     * @return string
     */
    public static function getDefaultStatus()
    {
        return static::STATUS_DRAFT;
    }

    /**
     * Fire events when create, update, delete teams
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // When delete record
        self::deleted(function ($model) {
            // Detach reference tags
            /*$model->detachTags($model);*/
        });

    }

    /**
     * Detach the tags in a post
     *
     * @return int
     */
    public function detachTags()
    {
        /*// Remove old references
        $deleted = ReferenceTag::where('model', static::class)
            ->where('reference_id', $this->id)
            ->delete();

        return $deleted;*/
    }

}
