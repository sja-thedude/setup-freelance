<?php

namespace App\Modules\ContentManager\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\ContentManager\Models\Articles;
use App\Modules\ContentManager\Models\Terms;
use App\Modules\ContentManager\Models\Comments;
use Illuminate\Support\Facades\Redirect;

class DefaultController extends Controller
{
    public function index(){
        $post = 0;
        $page = 0;
        $category = 0;
        $comment = 0;

        return view("ContentManager::index",[
            'post'=>$post,
            'page'=>$page,
            'category'=>$category,
            'comment'=>$comment,
        ]);
    }

    /**
     * Clone translation data from another locale
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyFrom(Request $request)
    {
        $model = $request->get('model');
        $id = (int)$request->get('id');
        $locale = $request->get('locale');

        /** @var \App\Modules\ContentManager\Models\Articles $model */
        $model = new $model();
        $model = $model::findOrFail($id);
        $modelTrans = $model->translate($locale);

        // If not found translation data
        if (empty($modelTrans)) {
            $request->session()->flash('response', [
                'success' => false,
                'message' => array("Not found translation data.")
            ]);
            return Redirect::back();
        }

        foreach ($model->translatedAttributes as $attr) {
            $model->setAttribute($attr, $modelTrans->getAttribute($attr));
        }

        $model->save();

        $request->session()->flash('response', [
            'success' => true,
            'message' => array("Copy data successful.")
        ]);
        return Redirect::back();
    }
    
    /**
     * Clone translation data from another locale
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyFromPost(Request $request)
    {
        $input = $request->all();
        $models = $input['models'];
        $ids = $input['ids'];
        $locale = $input['locale'];        
        
        if(!empty($models)) {
            foreach($models as $key => $model) {
                $id = (int)$ids[$key];
                
                /** @var \App\Modules\ContentManager\Models\Articles $model */
                $model = new $model();
                $model = $model::findOrFail($id);
                $modelTrans = $model->translate($locale);

                // If not found translation data
                if (empty($modelTrans)) {
                    $request->session()->flash('response', [
                        'success' => false,
                        'message' => array("Not found translation data.")
                    ]);
                    return Redirect::back();
                }

                foreach ($model->translatedAttributes as $attr) {
                    $model->setAttribute($attr, $modelTrans->getAttribute($attr));
                }

                $model->save();
            }
        }                

        $request->session()->flash('response', [
            'success' => true,
            'message' => array("Copy data successful.")
        ]);
        return Redirect::back();
    }

}
