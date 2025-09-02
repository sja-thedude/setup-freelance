<?php

namespace App\Modules\ContentManager\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Terms extends Model
{
    use Translatable;

    protected $table = 'terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;
    protected $fillable = array('slug', 'name','taxonomy');

    /**
     * Set $translationModel if you want to overwrite the convention
     * for the name of the translation Model. Use full namespace if applied.
     *
     * The convention is to add "Translation" to the name of the class extending Translatable.
     * Example: City => CityTranslation
     */
    public $translationModel = '\App\Modules\ContentManager\Models\TermTranslation';

    /**
     * This is the foreign key used to define the translation relationship.
     * Set this if you want to overwrite the laravel default for foreign keys.
     *
     * @var
     */
    public $translationForeignKey = 'term_id';

    /**
     * For laravel-translatable
     * @var array
     */
    public $translatedAttributes = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    // (optionaly)
    // protected $with = ['translations'];

    public function children()
	{
	    return $this->hasMany('App\Modules\ContentManager\Models\Terms', 'parent', 'term_id');
	}

	public function parent()
	{
	    return $this->belongsTo('App\Modules\ContentManager\Models\Terms', 'parent');
	}

	private function termRelationships(){
        return $this->belongsToMany('App\Modules\ContentManager\Models\Articles', 'term_relationships','term_taxonomy_id','object_id');
    }

    public function posts(){
    	return $this->termRelationships()->where('post_status','publish')->where('post_type','post');
    }

    public function getUrl(){
    	return url('/'.$this->taxonomy)."/".$this->slug;
    }

	public function checkRelationPost($post_id){
		$count = TermRelationships::where("object_id",$post_id)->where("term_taxonomy_id",$this->term_id)->count();
		return ($count > 0) ? true : false ;
	}

    public function meta()
    {
        return $this->hasMany('App\Modules\ContentManager\Models\TermMeta','term_id');
    }

    public function getMetaValue($key){
        $model = $this->meta()->where('meta_key',$key)->first();
        if(count($model) > 0){
            return $model->meta_value;
        }
        return null;
    }

}
