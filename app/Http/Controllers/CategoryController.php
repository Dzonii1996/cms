<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\CategoryRelation;
use Illuminate\Http\Request;

class CategoryController extends Controller{

    public function allLangCategory() {
        $category = CategoryRelation::with('allLangCategory')->paginate(10);
        return response()->json($category);
    }

    public function index(string $lang) {
        $categories = Category::with('posts')->where('lang', $lang)->paginate(10);
        return response()->json($categories);
    }

    public function store(Request $request, string $lang,int $main_category_id = null) {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'lang' => 'required|unique:categories,lang,NULL,id,$main_category_id,' . $main_category_id
        ]);
        if(!isset($main_page_id)){
            $main_category = CategoryRelation::create();
            $main_category_id = $main_category->id;
        }

        $lang = $request->lang;
        $name = $request->name;
        $slug = Str::slug($name);

        $parent = $request->parent_id;

        if($parent){
            $parent_id = $parent;
        }else{
            $parent_id = null;
        }


        $category = Category::create([
            'main_category_id' => $main_category_id,
            'lang' => $lang,
            'name' => $name,
            'parent_id' => $parent_id,
            'slug' => $slug,

        ]);

        if($category){
            return response()->json($category);
        }else{
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

    public function update(Request $request, string $lang,int $category_id) {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
        ]);


        $name = $request->name;
        $slug = Str::slug($name);

        $category = Category::findOrFail($category_id)->update([
            'name' => $name,
            'slug' => $slug,

        ]);

        if($category){
            return response()->json($category);
        }else{
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

    public function show(string $lang, int $main_category_id){
        $category = CategoryRelation::where('id',$main_category_id)->with('category', 'post_relation')->first();

        return response()->json($category);
    }


    public function destroy($category) {
        $pages = Category::find($category)->forceDelete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function edit($category) {
        $categories = Category::where('id', $category)->get();

        return response()->json($categories);
    }

}
