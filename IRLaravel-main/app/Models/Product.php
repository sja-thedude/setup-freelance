<?php

namespace App\Models;

use App\Facades\Helper;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends AppModel
{
    use SoftDeletes;
    use Translatable;

    /**
     * String
     */
    const AVATAR = 'avatar';

    /**
     * @var string
     */
    public $translationModel = 'App\Models\ProductTranslation';

    /**
     * @var string
     */
    public $translationForeignKey = 'product_id';

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
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'workspace_id',
        'category_id',
        'vat_id',
        'currency',
        'price',
        'use_category_option',
        'time_no_limit',
        'active',
        'order',
        'created_at',
        'updated_at',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartOptionItems()
    {
        return $this->hasMany(\App\Models\CartOptionItem::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function vat()
    {
        return $this->belongsTo(\App\Models\Vat::class, 'vat_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOptions()
    {
        return $this->hasMany(\App\Models\ProductOption::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productSuggestions()
    {
        return $this->hasMany(\App\Models\ProductSuggestion::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function options()
    {
        return $this->belongsToMany(\App\Models\Option::class, 'product_opties', 'product_id', 'opties_id')->withPivot('is_checked')
            ->with('items');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productLabels()
    {
        return $this->hasMany(\App\Models\ProductLabel::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productLabelsActive()
    {
        return $this->hasMany(\App\Models\ProductLabel::class, 'product_id')->where('active', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productAllergenens()
    {
        return $this->hasMany(\App\Models\ProductAllergenen::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allergenens()
    {
        return $this->belongsToMany(\App\Models\Allergenen::class, 'product_allergenens');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function productFavorites()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'product_favorites',
            'product_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productAvatar()
    {
        return $this->hasOne(\App\Models\Media::class, 'foreign_id')
            ->where('foreign_model', Product::class)
            ->where('foreign_type', Product::AVATAR);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openTimeslots()
    {
        return $this->hasMany(\App\Models\OpenTimeslot::class, 'foreign_id')
            ->where('foreign_model', Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\ProductTranslation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productReferences()
    {
        return $this->hasMany(\App\Models\ProductReference::class, 'local_id');
    }

    /**
     * @return array
     */
    public function getSummaryInfo()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name, // From translation table
        ];
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return array_merge(parent::getFullInfo(), [
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'deleted_at' => Helper::getDatetimeFromFormat($this->deleted_at, 'Y-m-d H:i:s'),
            'active' => (!empty($this->active) && empty($this->deleted_at)),
            'name' => $this->name, // in the translation table
            'description' => $this->description, // in the translation table
            'workspace_id' => $this->workspace_id,
            'workspace' => $this->workspace->getSummaryInfo(),
            'category_id' => $this->category_id,
            'category' => (!empty($this->category)) ? $this->category->getSummaryInfo() : null,
            'vat_id' => $this->vat_id,
            'vat' => (!empty($this->vat)) ? $this->vat->getSummaryInfo() : null,
            'currency' => $this->currency,
            'price' => $this->price,
            'use_category_option' => $this->use_category_option,
            'time_no_limit' => $this->time_no_limit,
            'is_suggestion' => $this->is_suggestion,
            'order' => (!empty($this->order)) ? $this->order : 0,
            'photo' => $this->photo,
            'photo_path' => $this->photo_path,
            // Product allergenens
            'allergenens' => $this->allergenens->transform(function ($allergenen) {
                /** @var \App\Models\Allergenen $allergenen */
                return (is_array($allergenen)) ? $allergenen : $allergenen->getFullInfo();
            }),
            // Product labels
            'labels' => $this->productLabelsActive->transform(function ($label) {
                /** @var \App\Models\ProductLabel $label */
                return (is_array($label)) ? $label : $label->getFullInfo();
            }),
            // Product favorites
            'productFavorites' => $this->productFavorites
        ]);
    }

    /**
     * Mutator photo
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->productAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->full_path;
    }

    /**
     * Mutator photo
     *
     * @return string
     */
    public function getPhotoPathAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->productAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->file_path;
    }
}
