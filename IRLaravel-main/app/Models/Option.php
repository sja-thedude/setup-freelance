<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends AppModel
{
    use SoftDeletes;
    use Translatable;

    /**
     * @var int
     */
    const YES = 1;

    /**
     * @var int
     */
    const NO = 0;

    /**
     * @var string
     */
    public $translationModel = 'App\Models\OptionTranslation';

    /**
     * @var string
     */
    public $translationForeignKey = 'opties_id';

    /**
     * @var array
     */
    public $translatedAttributes = [
        'name',
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
    public $table = 'opties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'workspace_id',
        'min',
        'max',
        'type',
        'is_ingredient_deletion',
        'order',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $casts = [
        'workspace_id' => 'integer',
        'min' => 'integer',
        'max' => 'integer',
        'type' => 'integer',
        'is_ingredient_deletion' => 'boolean',
        'order' => 'integer',
    ];

    const TYPE_ADD = 1;
    const TYPE_SUB = 0;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function optionItems()
    {
        return $this->hasMany(\App\Models\OptionItem::class, 'opties_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartOptionItems()
    {
        return $this->hasMany(\App\Models\CartOptionItem::class, 'optie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(\App\Models\OptionItem::class, 'opties_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryOptions()
    {
        return $this->hasMany(\App\Models\CategoryOption::class, 'opties_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOptions()
    {
        return $this->hasMany(\App\Models\ProductOption::class, 'opties_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\OptionTranslation::class, 'opties_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return $this->type ? trans('option.ja') : trans('option.nee');
    }

    /**
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        return $this->type_name;
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'deleted_at' => $this->deleted_at,
            'workspace_id' => $this->workspace_id,
            'workspace' => (!empty($this->workspace_id) && !empty($this->workspace)) ? $this->workspace->getSummaryInfo() : null,
            'name' => $this->name, // from translations table
            'min' => $this->min,
            'max' => $this->max,
            'type' => $this->type,
            'type_display' => $this->type_display,
            'is_ingredient_deletion' => $this->is_ingredient_deletion,
            'order' => (!empty($this->order)) ? $this->order : 0,
        ];
    }

    /**
     * @param $workspaceId
     * @return array
     */
    public static function getOptionsList($workspaceId) {
        $data = Option::with('translations')
            ->where('workspace_id', $workspaceId)
            ->get()
            ->pluck('name', 'id')->toArray();
        
        return $data;
    }
}
