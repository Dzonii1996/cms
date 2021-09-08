<?php

namespace App\Http\Controllers;

use App\Helpers\Langugages\LangCheck;
use App\Helpers\Media\MediaUpload;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\PostTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Helpers\Media\featuredImage;
use App\Models\Status;


class PostController extends Controller
{

    public function index(Request  $request, $lang = null) {
        if (LangCheck::LangCheckIfExist($lang)) {
            return LangCheck::LangCheckIfExist($lang);
        }

        $per_page = $request->per_page;
        $search = $request->search;

        $posts = Post::with(['post_content' => function ($query) use ($lang) {
            if ($lang !== null) {
                return $query->where('lang', $lang);
            } else {
                return $query;
            }
        },
            'post_content.status',
            'author',
            'featured_image',
            'categories.category_content',
        ])
            ->whereHas(
                'post_content', function ($query) use ($lang, $search) {
                if ($lang !== null) {
                    return $query->where('lang', $lang)->where('title', 'like', '%' . $search . '%');
                } else {
                    return $query->where('title', 'like', '%' . $search . '%');
                }
            })
            ->paginate($per_page);

        return response()->json($posts);

    }



    public function store(Request $request, string $lang,int $main_post_id = null) {
        $request->validate([
            'title' => 'required|unique:posts|max:255',
            'status_id' => 'required|exists:statuses,id'
        ]);

        $lang = $request->lang;
        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->post_content;
        $featured_image = $request->file('featured_image');
        $categories = $request->categories;
        $status_id = $request->status_id;
        $post_id = $request->post_id;
        $audio = $request->file('audio');


        if ($featured_image) {
            $image_id = MediaUpload::uploadMedia($featured_image);
        } else {
            $image_id = null;
        }

        if ($audio) {
            $audio_id = MediaUpload::uploadMedia($audio);
        } else {
            $audio_id = null;
        }

        if (!isset($post_id)) {
            $post = Post::create([
                'featured_image_id' => $image_id,
                'author_id' => Auth::user()->id,
            ]);
            $post_id = $post->id;
        }

        if ($lang != null) {
            $request->validate([
                'lang' => 'required|unique:post_translations,lang,NULL,id,post_id,' . $post_id
            ]);
        }

        if ($get_content) {
            $content = $get_content;
        } else {
            $content = null;
        }

        if (!empty($post_id)) {
            $post_translation = PostTranslation::create([
                'post_id' => $post_id,
                'lang' => $lang,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'audio_id' => $audio_id,
                'status_id' => $status_id,
            ]);
        }


        DB::table('categories_posts')->where('post_id', $post_id)->delete();

        if (!empty($categories)) {
            foreach ($categories as $category_id) {
                DB::table('categories_posts')->insert([
                    'category_id' => $category_id,
                    'post_id' => $post_id,
                ]);
            }
        }

        if (!empty($post_translation)) {
            return response()->json([$post_translation]);
        } else {
            return response()->json(['error' => 'Post not created'], 422);
        }
    }

    public function show($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->showPostFilter($param2, $param1);
        } else {
            return $this->showPostFilter($param1);
        }
    }

    public function showPostFilter($post_id, $lang = null)
    {

        if (LangCheck::LangCheckIfExist($lang)) {
            return LangCheck::LangCheckIfExist($lang);
        }

        $post = Post::where('id', $post_id)
            ->with(['post_content' => function ($query) use ($lang) {
                if ($lang !== null) {
                    return $query->where('lang', $lang);
                } else {
                    return $query;
                }
            },
                'post_content.status',
                'author',
                'featured_image',
                'categories.category_content' => function ($query) use ($lang) {
                    if ($lang !== null) {
                        return $query->where('lang', $lang);
                    } else {
                        return $query;
                    }
                },
            ])
            ->whereHas(
                'post_content', function ($query) use ($lang) {
                if ($lang !== null) {
                    return $query->where('lang', $lang);
                } else {
                    return $query;
                }
            })->first();

        if ($post) {
            return response()->json($post);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }
    }

    public function update(Request $request, $param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->updatePostFilter($request, $param2, $param1);
        } else {
            return $this->updatePostFilter($request, $param1);
        }
    }


    public function updatePostFilter($request, $post_id, $lang = null)
    {
        $request->validate([
            'title' => ['required', Rule::unique('post_translations')->ignore($post_id, 'post_id')],
            'status_id' => 'required|exists:statuses,id'
        ]);

        $lang = $request->lang;
        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->post_content;
        $featured_image = $request->file('featured_image');
        $featured_image_id = $request->featured_image_id;
        $status_id = $request->status_id;
        $categories = $request->categories;
        $audio = $request->file('audio');
        $old_audio_id = $request->audio_id;

        if ($featured_image) {
            $image_id = MediaUpload::uploadMedia($featured_image);
        } else if ($featured_image_id) {
            $image_id = $featured_image_id;
        } else {
            $image_id = null;
        }

        if ($audio) {
            $audio_id = MediaUpload::uploadMedia($audio);
        } else if ($old_audio_id) {
            $audio_id = $old_audio_id;
        } else {
            $audio_id = null;
        }

        if ($get_content) {
            $content = $get_content;
        } else {
            $content = null;
        }

        $post = Post::where('id', $post_id)->first();
        if ($lang != null) {
            $post_translation = PostTranslation::where('post_id', $post_id)->where('lang', $lang)->first();
        } else {
            $post_translation = PostTranslation::where('post_id', $post_id)->first();
        }


        if ($post && $post_translation) {
            $post->update([
                'featured_image_id' => $image_id,
                'author_id' => Auth::user()->id,
            ]);


            $post_translation->update([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'audio_id' => $audio_id,
                'status_id' => $status_id,
            ]);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }


        $main_post_id = $post->main_post_id;

        DB::table('categories_posts')->where('post_id', $main_post_id)->delete();

        if ($categories) {
            foreach ($categories as $category_id) {
                DB::table('categories_posts')->insert([
                    'category_id' => $category_id,
                    'post_id' => $main_post_id,
                ]);
            }
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);

    }

    public function destroy($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->destroyPostFilter($param2, $param1);
        } else {
            return $this->destroyPostFilter($param1);
        }
    }

    public function destroyPostFilter($post_id, $lang = null)
    {
        if ($lang != null) {
            $post_translation = PostTranslation::where('post_id', $post_id)->where('lang', $lang)->first();
        } else {
            $post_translation = PostTranslation::where('post_id', $post_id)->first();
        }

        if ($post_translation) {
            $post_translation->delete();
            return response()->json(['message' => 'Post successfully moved to trash']);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }

    }

    public function trash(Request $request, string $lang = null)
    {
        $per_page = $request->per_page;

        if ($lang != null) {
            $posts = PostTranslation::onlyTrashed()->where('lang', $lang)->paginate($per_page);
        } else {
            $posts = PostTranslation::onlyTrashed()->paginate($per_page);
        }
        if ($posts) {
            return response()->json($posts);
        } else {
            return response()->json(['message' => 'No posts have been trashed']);
        }

    }

    public function restore($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->restorePostFilter($param2, $param1);
        } else {
            return $this->restorePostFilter($param1);
        }
    }

    public function restorePostFilter($post_id, $lang = null)
    {
        if ($lang != null) {
            $post_translation = PostTranslation::onlyTrashed()->where('post_id', $post_id)->where('lang', $lang)->first();
        } else {
            $post_translation = PostTranslation::onlyTrashed()->where('post_id', $post_id)->first();
        }

        if ($post_translation) {
            $post_translation->restore();
            return response()->json(['post_id' => $post_id, 'message' => 'Post restored successfully']);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }

    }

    public function delete($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->deletePostFilter($param2, $param1);
        } else {
            return $this->deletePostFilter($param1);
        }
    }

    public function deletePostFilter($post_id, $lang = null)
    {
        if ($lang != null) {
            $post_translation = PostTranslation::onlyTrashed()->where('post_id', $post_id)->where('lang', $lang)->first();
        } else {
            $post_translation = PostTranslation::onlyTrashed()->where('post_id', $post_id)->first();
        }

        if ($post_translation) {
            $post_translation->forceDelete();
            return response()->json(['message' => 'Post successfully deleted']);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }
    }
}
