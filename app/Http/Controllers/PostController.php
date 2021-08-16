<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function createPost(Request $request)
    {
        $post = new Post();
        $post->title = $request->title;
        $post->excerpt = $request->excerpt;
        $post->body = $request->body;
        $post->save();

        return response()->json([
            "mesage" => "post created"
        ], 201);

    }

    public function getPost()
    {
        $post = Post::all();
        return response()->json($post);
    }

    public function updatePost(Request $request, $id)
    {


    }

    public function deletePost($id)
    {
    }
}
