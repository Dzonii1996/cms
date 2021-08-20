<?php

namespace App\Http\Controllers;
use App\Models\PostRelation;
use Illuminate\Support\Str;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\Media\featuredImage;
class PostController extends Controller
{

    public function index() {
        $posts = Post::with('category', 'featured_image')->paginate(10);

        return response()->json($posts);
    }


    public function store(Request $request,int $main_post_id=null)
    {
        $request->validate([
            'title' => 'required|unique:posts|max:255',

        ]);

        if(!isset($main_post_id)){
            $main_post = PostRelation::create();
            $main_post_id = $main_post->id;
        }
        $title=$request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->post_content;
        $featured_image = $request->file('featured_image');


if($featured_image){
$image_id = FeaturedImage::uploadFeaturedImage($featured_image);
} else{
    $image_id = null;
}
if($get_content){
    $content = $get_content;
}else {
    $content = null;
}

        $post = Post::create([
            'main_post_id' => $main_post_id,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featured_image_id' => $image_id,
        ]);


        if($post){
            return response()->json($post);
        }else{
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function delete( $post_id) {
        Post::withTrashed()->findOrFail($post_id)->forceDelete();
        return response()->json(['post_id' => $post_id , 'message' => 'Post deleted successfully']);
    }
}
