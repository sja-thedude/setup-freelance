<?php

namespace App\Modules\ContentManager\Models;

use App\Helpers\Helper;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';
    public $fillable = [
        'post_author',
        'post_type',
        'post_name',
        'post_content',
        'post_title',
        'post_hit'
    ];

    /**
     * Set $translationModel if you want to overwrite the convention
     * for the name of the translation Model. Use full namespace if applied.
     *
     * The convention is to add "Translation" to the name of the class extending Translatable.
     * Example: City => CityTranslation
     */
    public $translationModel = 'App\Modules\ContentManager\Models\ArticleTranslation';

    /**
     * This is the foreign key used to define the translation relationship.
     * Set this if you want to overwrite the laravel default for foreign keys.
     *
     * @var
     */
    public $translationForeignKey = 'post_id';

    /**
     * For laravel-translatable
     * @var array
     */
    public $translatedAttributes = [
        'post_content',
        'post_title',
        'post_excerpt',
        'post_name'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    // (optionaly)
    // protected $with = ['translations'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'post_author');
    }

    public function comments(){
        return $this->hasMany('App\Modules\ContentManager\Models\Comments', 'post_id')->where("approved",1);
    }

    public function meta()
    {
        return $this->hasMany('App\Modules\ContentManager\Models\ArticleMeta','post_id');
    }

    private function termRelationships(){
        return $this->belongsToMany('App\Modules\ContentManager\Models\Terms', 'term_relationships','object_id','term_taxonomy_id');
    }

    public function categories(){
        return $this->termRelationships()->where("taxonomy","category");
    }

    public function tags(){
        return $this->termRelationships()->where("taxonomy","tag");
    }

    public function getMetaValue($key){
        $model = $this->meta()->where('meta_key',$key)->first();
        if(count($model) > 0){
            return $model->meta_value;
        }
        return null;
    }

    public function getExcerpt($limit = 40){
        if(!empty($this->post_excerpt)){
            return strip_tags($this->post_excerpt);
        }

        $content = strip_tags($this->post_content);

        $excerpt = explode(' ', $content, $limit);
        if (count($excerpt)>=$limit) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt).'...';
        } else {
            $excerpt = implode(" ",$excerpt);
        } 
            $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
        return $excerpt;
    }

    public function getContent(){
        return $this->post_content;
    }

    public function getUrl($post = "post"){
        if($post == "post"){
            return url('/')."/".$this->post_name;
        }else{
            return url('/')."/".$post."/".$this->post_name;
        }
    }

    public function cleanContent($content){
        $style = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $face = preg_replace('/(<[^>]+) face=".*?"/i', '$1', $style);
        $color = preg_replace('/(<[^>]+) color=".*?"/i', '$1', $face);
        return $color;
    }

    public function getFullInfo()
    {
        return [
            'id' => $this->id,
            'content' => $this->post_content,
            'title' => $this->post_title,
            'excerpt' => $this->post_excerpt,
            'name' => $this->post_name,
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
        ];
    }
}
