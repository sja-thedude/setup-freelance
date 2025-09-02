<?php

namespace App\Modules\ContentManager\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    use Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * Set $translationModel if you want to overwrite the convention
     * for the name of the translation Model. Use full namespace if applied.
     *
     * The convention is to add "Translation" to the name of the class extending Translatable.
     * Example: City => CityTranslation
     */
    public $translationModel = '\App\Modules\ContentManager\Models\ArticleTranslation';

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

    public function children(){
        $child = Menus::join('post_meta', 'posts.id', '=', 'post_meta.post_id')
                ->select('posts.*', 'post_meta.meta_key', 'post_meta.meta_value')
                ->where('post_meta.meta_key','_nav_item_parent')
                ->where('post_meta.meta_value',$this->id)
                ->orderBy('posts.menu_order', 'asc')
                ->get();
        return $child;    
    }

    public function getMetaValue($key){
        $model = ArticleMeta::where('post_id', $this->id)->where('meta_key', $key)->first();
        if(count($model) > 0){
            return $model->meta_value;
        }

        return null;
    }

    public function getURL(){
        $type = $this->getMetaValue("_nav_item_type");
        $res = "#";
        switch ($type) {
            case 'home':
                $res =  url('/');
                break;
            case 'category':
                $res = url('/category/'.$this->post_name);
                break;
            case 'custom':
                $res = $this->getMetaValue('_nav_item_url');
                break; 
            case 'page':
                $res = url('/'.$this->post_name.'.html');
                break;        
            
            default:
                $res = url('/'.$this->post_name);
                break;
        }
        return $res;
    }

}
