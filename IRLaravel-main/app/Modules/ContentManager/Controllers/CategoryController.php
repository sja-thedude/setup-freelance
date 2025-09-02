<?php

namespace App\Modules\ContentManager\Controllers;

use Illuminate\Http\Request;
use App\Modules\ContentManager\Models\Terms;
use App\Modules\ContentManager\Models\TermRelationships;
use App\Facades\Admin;
use App\Http\Controllers\Controller;
use App\Facades\Theme;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = null;
        // Paging categories
        $modelAll = Terms::where("taxonomy","category")->orderBy('term_id', 'desc')->paginate(10);
        // List parent categories
        $category = Terms::where("taxonomy","category")
            ->where("parent",0)
            ->get();
        return view("ContentManager::category.index",['model' => $model,"category"=>$category,"modelAll"=>$modelAll]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new Terms();
        $this->validate($request, [
            'name' => 'required',
            'parent' => 'required',
        ]);
        $model->name = $request->name;
        if(!empty(trim($request->slug))):
            $model->slug = str_slug($request->slug,"-");
        else:
            $model->slug = str_slug($request->name,"-");
        endif;
        $model->term_group = 0;
        $model->taxonomy = "category";
        $model->description = $request->description;
        $model->parent = $request->parent;
        $model->save();

        // Prepare for Remove domain in featured_img
        $baseUrl = url('/') . '/';
        $baseUrlLength = strlen($baseUrl);

        foreach ($request->meta as $key => $value) {
            // Remove domain in featured_img
            if (in_array($key, ['featured_img'])) {
                $value = substr($value, $baseUrlLength);
            }

            $model->meta()->updateOrCreate(["meta_key"=>$key],["meta_key"=>$key,"meta_value"=>$value]);
        }

        if($request->ajax()){
            return json_encode(["id"=>$model->term_id,"parent"=>$model->parent]);
        }
        return redirect(Admin::StrURL('contentManager/category'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $model = Terms::whereTranslation("slug",$slug)->where('taxonomy','category')->firstOrFail();
        if (view()->exists(Theme::active().'.post.archive')) {
            return view(Theme::active().'.post.archive',['model'=>$model->posts()->paginate(10),'appTitle'=>$model->name, 'category' => $model]);
        }else{
            return view("ContentManager::category.show",['model'=>$model,'appTitle'=>$model->name]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Terms::find($id);
        $modelAll = Terms::where("taxonomy","category")->orderBy('term_id', 'desc')->paginate(10);
        $category = Terms::where("taxonomy","category")
            ->where("parent",0)
            ->where("term_id", '!=', $id)
            ->get();
        return view("ContentManager::category.update",['model' => $model,"category"=>$category,"modelAll"=>$modelAll]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = Terms::find($id);
        $this->validate($request, [
            'name' => 'required',
            'parent' => 'required',
        ]);
        $model->name = $request->name;
        if(!empty(trim($request->slug))):
            $model->slug = str_slug($request->slug,"-");
        else:
            $model->slug = str_slug($request->name,"-");
        endif;
        $model->term_group = 0;
        $model->taxonomy = "category";
        $model->description = $request->description;
        $model->parent = $request->parent;
        $model->save();

        // Prepare for Remove domain in featured_img
        $baseUrl = url('/') . '/';
        $baseUrlLength = strlen($baseUrl);

        foreach ($request->meta as $key => $value) {
            // Remove domain in featured_img
            if (in_array($key, ['featured_img'])) {
                $value = substr($value, $baseUrlLength);
            }

            $model->meta()->updateOrCreate(["meta_key"=>$key],["meta_key"=>$key,"meta_value"=>$value]);
        }

        return redirect(Admin::StrURL('contentManager/category'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tmp = explode(",", $id);
        if(is_array($tmp)){
            Terms::destroy($tmp);
            foreach ($tmp as $value) {
                TermRelationships::where("term_taxonomy_id",$value)->delete();
            }
        }else{
            Terms::destroy($id);  
            TermRelationships::where("term_taxonomy_id",$id)->delete();
        }
    }
}
