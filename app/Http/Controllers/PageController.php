<?php

namespace App\Http\Controllers;

use App\Models\PageRelation;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Media\featuredImage;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function allLangPages()
    {
        $pages = PageRelation::with('allLangPages')->paginate(10);
        return response()->json($pages);
    }

    public function index(string $lang)
    {
        $pages = Page::with('featured_image', 'status', 'author')->where('lang', $lang)->paginate(10);
        return $pages;
    }

    public function store(Request $request, string $lang, int $main_page_id = null)
    {
        $request->validate([
            'title' => 'required|unique:pages|max:255',
            'lang' => 'required|unique:pages,lang,NULL,id,main_page_id,' . $main_page_id
        ]);
        if (!isset($main_page_id)) {
            $main_page = PageRelation::create();
            $main_page_id = $main_page->id;
        }

        $lang = $request->lang;
        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->page_content;
        $featured_image = $request->file('featured_image');

        if ($featured_image) {
            $image_id = FeaturedImage::uploadFeaturedImage($featured_image);

        } else {
            $image_id = null;
        }
        if ($get_content) {
            $content = $get_content;
        } else {
            $content = null;
        }

        $page = Page::create([
            'main_page_id' => $main_page_id,
            'lang' => $lang,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featured_image_id' => $image_id,
            'author_id' => Auth::user()->id,
            'status_id' => Status::STATUS_PUBLISH,
        ]);

        if ($page) {
            return response()->json($page);
        } else {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function show(string $lang, int $main_page_id)
    {
        $page = Page::where(['main_page_id' => $main_page_id, 'lang' => $lang])->with('author', 'featured_image', 'status')->first();

        return response()->json($page);
    }

    public function update(Request $request, string $lang, int $page_id)
    {
        $request->validate([
            'title' => ['required', Rule::unique('pages')->ignore($page_id)],
            'status_id' => 'required'
        ]);

        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->page_content;
        $featured_image = $request->file('featured_image');
        $old_image = $request->old_image;
        $status_id = $request->status_id;

        if ($featured_image) {
            $image_id = FeaturedImage::uploadFeaturedImage($featured_image);
        } elseif ($old_image != 'null') {
            $image_id = $old_image;
        } else {
            $image_id = null;
        }

        if ($get_content) {
            $content = $get_content;
        } else {
            $content = null;
        }

        $page = Page::findOrFail($page_id)->update([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featured_image_id' => $image_id,
            'status_id' => $status_id,
        ]);

        if ($page) {
            return response()->json(['message' => 'Page updated successfully']);
        } else {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function destroy(string $lang, int $page_id)
    {
        Page::findOrFail($page_id)->delete();
        return response()->json(['page_id' => $page_id, 'message' => 'Page successfully moved to trash']);
    }

    public function trash(string $lang)
    {
        $pages = Page::onlyTrashed()->where('lang', $lang)->paginate(10);
        return response()->json($pages);
    }

    public function restore(string $lang, $page_id)
    {
        Page::withTrashed()->findOrFail($page_id)->restore();
        return response()->json(['page_id' => $page_id, 'message' => 'Page restored successfully']);
    }

    public function delete(string $lang, $page_id)
    {
        Page::withTrashed()->findOrFail($page_id)->forceDelete();
        return response()->json(['page_id' => $page_id, 'message' => 'Page deleted successfully']);
    }

}
