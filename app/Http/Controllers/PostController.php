<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Helpers\Media\featuredImage;
use App\Models\Status;


class PostController extends Controller
{
    public function allLangPosts() {
        $posts = PostRelation::with('allLangPosts')->paginate(10);
        return response()->json($posts);
    }

    public function index(string $lang) {
        $posts = Post::with('category','author', 'featured_image', 'status')->where('lang', $lang)->paginate(10);

        return response()->json($posts);
    }

    public function store(Request $request, string $lang,int $main_post_id = null) {
        $request->validate([
            'title' => 'required|unique:posts|max:255',
            'lang' => 'required|unique:posts,lang,NULL,id,main_post_id,' . $main_post_id

        ]);

        if(!isset($main_post_id)){
            $main_post = PostRelation::create();
            $main_post_id = $main_post->id;
        }

        $lang = $request->lang;
        $title = $request->title;
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
            'lang' => $lang,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featured_image_id' => $image_id,
            'author_id' => Auth::user()->id,
            'status_id' => Status::STATUS_PUBLISH,
        ]);

        if($post){
            return response()->json($post);
        }else{
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function show(string $lang, int $main_post_id){
        $post = Post::where(['main_post_id'=> $main_post_id, 'lang' => $lang])->with('category','author', 'featured_image', 'status')->firstOrFail();

        return response()->json($post);
    }

    public function update(Request $request, string $lang, int $post_id) {
        $request->validate([
            'title' => ['required', Rule::unique('posts')->ignore($post_id)],
            'status_id' => 'required'
        ]);

        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->post_content;
        $featured_image = $request->file('featured_image');
        $old_image = $request->old_image;
        $status_id = $request->status_id;

        if($featured_image){
            $image_id = FeaturedImage::uploadFeaturedImage($featured_image);
        } elseif ($old_image != 'null') {
            $image_id = $old_image;
        } else {
            $image_id = null;
        }

        if($get_content){
            $content = $get_content;
        }else {
            $content = null;
        }

        $post = Post::findOrFail($post_id)->update([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featured_image_id' => $image_id,
            'status_id' => $status_id,
        ]);

        if($post){
            return response()->json(['message' => 'Post updated successfully']);
        }else{
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

    public function destroy(string $lang, int $post_id) {
        $post = Post::findOrFail($post_id)->delete();
        return response()->json(['post_id' => $post_id ,'message' => 'Post successfully moved to trash']);
    }

    public function trash(string $lang) {
        $posts = Post::onlyTrashed()->where('lang', $lang)->paginate(10);
        return response()->json($posts);
    }

    public function restore(string $lang, $post_id){
        Post::withTrashed()->findOrFail($post_id)->restore();
        return response()->json(['post_id' => $post_id , 'message' => 'Post restored successfully']);
    }

    public function delete(string $lang, $post_id) {
        Post::withTrashed()->findOrFail($post_id)->forceDelete();
        return response()->json(['post_id' => $post_id , 'message' => 'Post deleted successfully']);
    }
}
