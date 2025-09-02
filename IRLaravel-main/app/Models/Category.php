<?php

namespace App\Models;

use App\Facades\Helper;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends AppModel
{
    use SoftDeletes;
    use Translatable;

    const ACTIVE = 1;
    const INACTIVE = 0;
    const AVATAR = 'avatar';

    const LIMIT_TIME_YES = 1;
    const TIME_LIMIT_NO = 0;

    /**
     * @var string
     */
    public $translationModel = 'App\Models\CategoryTranslation';

    /**
     * @var string
     */
    public $translationForeignKey = 'category_id';

    /**
     * @var array
     */
    public $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:255'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'workspace_id',
        'individual',
        'group',
        'available_delivery',
        'available_in_house',
        'exclusively_in_house',
        'favoriet_friet',
        'kokette_kroket',
        'time_no_limit',
        'active',
        'order',
        'extra_werkbon',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'workspace_id' => 'integer',
        'available_delivery' => 'boolean',
        'available_in_house' => 'boolean',
        'exclusively_in_house' => 'boolean',
        'favoriet_friet' => 'boolean',
        'kokette_kroket' => 'boolean',
        'time_no_limit' => 'boolean',
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryOptions()
    {
        return $this->hasMany(\App\Models\CategoryOption::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'category_id');
    }

    public function productIncTrash()
    {
        return $this->hasMany(\App\Models\Product::class, 'category_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\CategoryTranslation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productSuggestions()
    {
        return $this->hasMany(\App\Models\ProductSuggestion::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function categoryAvatar()
    {
        return $this->hasOne(
            \App\Models\Media::class,
            'foreign_id'
        )
        ->where('foreign_model', Media::MODEL_CATEGORY)
        ->where('foreign_type', Media::AVATAR);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openTimeslots()
    {
        return $this->hasMany(
            \App\Models\OpenTimeslot::class,
            'foreign_id'
        )
        ->where('foreign_model', Category::class);
    }

    /**
     * @return array
     */
    public function getSummaryInfo()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'favoriet_friet' => !empty($this->favoriet_friet),
            'kokette_kroket' => !empty($this->kokette_kroket)
        ];
    }

    /**
     * Mutator photo
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->categoryAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->full_path;
    }

    /**
     * Mutator photo path
     *
     * @return string
     */
    public function getPhotoPathAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->categoryAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->file_path;
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return array_merge(parent::getFullInfo(), [
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'name' => $this->name, // in the translation table
            'description' => $this->description, // in the translation table
            'workspace_id' => $this->workspace_id,
            'workspace' => $this->workspace->getSummaryInfo(),
            'available_delivery' => !empty($this->available_delivery),
            'available_in_house' => !empty($this->available_in_house),
            'exclusively_in_house' => !empty($this->exclusively_in_house),
            'favoriet_friet' => !empty($this->favoriet_friet),
            'kokette_kroket' => !empty($this->kokette_kroket),
            'order' => (int)$this->order,
            'photo' => $this->photo,
            'photo_path' => $this->photo_path,
        ]);
    }

    public function categoriesRelation()
    {
        return $this->hasMany(CategoryRelation::class,'foreign_id')
            ->where('foreign_model', static::class);
    }
}
