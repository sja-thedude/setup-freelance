<?php

namespace App\Http\Controllers;

use App\Modules\ContentManager\Models\Articles;
use App\Modules\ContentManager\Models\Terms;
use App\Facades\Theme;

class PostController extends Controller
{
    public function index(){
        $res = array();
        $data = Articles::where('post_type','post')->orderBy('id', 'desc')->get();
       
        foreach ($data as $value) {
            $res[] = [
                'id'=>$value->id,
                'excerpt'=>$value->post_excerpt,
                'title'=>$value->post_title,
                'created'=>$value->created_at,
                'content'=>$value->post_content,
                'image'=>$value->getMetaValue('featured_img'),
            ];
        }

        return response()->json(['datas' => $res]);
    }

    public function view($slug){
        $model = Articles::where("post_name",$slug)->firstOrFail();
    	return view(Theme::active().'.post.view',["model"=>$model]);
    }

    public function category($slug){
    	$model = Terms::where("slug",$slug)->first();
        print_r($model);
    }
}
