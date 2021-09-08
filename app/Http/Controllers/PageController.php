<?php

namespace App\Http\Controllers;

use App\Helpers\Langugages\LangCheck;
use App\Helpers\Media\MediaUpload;
use App\Models\PageTranslation;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Media\featuredImage;
use Illuminate\Validation\Rule;

class PageController extends Controller
{


    public function index(Request $request, $lang = null)
    {
        if (LangCheck::LangCheckIfExist($lang)) {

            return LangCheck::LangCheckIfExist($lang);
        }

        $per_page = $request->per_page;
        $search = $request->search;

        $pages = Page::with(['page_content' => function ($query) use ($lang) {
            if ($lang !== null) {
                return $query->where('lang', $lang);
            } else {
                return $query;
            }
        },
            'page_content.status',
            'author',
            'featured_image'
        ])
            ->whereHas(
                'page_content', function ($query) use ($lang, $search) {
                if ($lang !== null) {
                    return $query->where('lang', $lang)->where('title', 'like', '%' . $search . '%');
                } else {
                    return $query->where('title', 'like', '%' . $search . '%');
                }
            })
            ->paginate($per_page);

        return response()->json($pages);

    }


    public function store(Request $request, string $lang = null)
    {
        $request->validate([
            'title' => 'required|unique:page_translations|max:255',
            'status_id' => 'required|exists:statuses,id'
        ]);

        $lang = $request->lang;
        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $body_content = $request->body_content;
        $featured_image = $request->file('featured_image');
        $status_id = $request->status_id;
        $page_id = $request->page_id;

        if ($featured_image) {
            $image_id = MediaUpload::uploadMedia($featured_image);
        } else {
            $image_id = null;
        }

        if (!isset($page_id)) {
            $page = Page::create([
                'featured_image_id' => $image_id,
                'author_id' => Auth::user()->id,
            ]);
            $page_id = $page->id;
        }

        if ($lang != null) {
            $request->validate([
                'lang' => 'required|unique:page_translations,lang,NULL,id,page_id,' . $page_id
            ]);
        }

        if (!$body_content) {
            $body_content = null;
        }

        if (!empty($page_id)) {
            $page_translation = PageTranslation::create([
                'page_id' => $page_id,
                'lang' => $lang,
                'title' => $title,
                'slug' => $slug,
                'content' => $body_content,
                'status_id' => $status_id,
            ]);
        }

        if (!empty($page_translation)) {
            return response()->json([$page_translation]);
        } else {
            return response()->json(['error' => 'Page not created'], 422);
        }
    }

    public function show($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->showPageFilter($param2, $param1);
        } else {
            return $this->showPageFilter($param1);
        }
    }

    public function showPageFilter($page_id, $lang = null)
    {

        if (LangCheck::LangCheckIfExist($lang)) {
            return LangCheck::LangCheckIfExist($lang);
        }

        $page = Page::where('id', $page_id)
            ->with(['page_content' => function ($query) use ($lang) {
                if ($lang !== null) {
                    return $query->where('lang', $lang);
                } else {
                    return $query;
                }
            },
                'page_content.status',
                'author',
                'featured_image'
            ])
            ->whereHas(
                'page_content', function ($query) use ($lang) {
                if ($lang !== null) {
                    return $query->where('lang', $lang);
                } else {
                    return $query;
                }
            })->first();

        if ($page) {
            return response()->json($page);
        } else {
            return response()->json(['message' => 'Page not found'], 404);
        }
    }

    public function update(Request $request, $param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->updatePageFilter($request, $param2, $param1);
        } else {
            return $this->updatePageFilter($request, $param1);
        }
    }


    public function updatePageFilter($request, $page_id, $lang = null)
    {
        $request->validate([
            'title' => ['required', Rule::unique('page_translations')->ignore($page_id, 'page_id')],
            'status_id' => 'required|exists:statuses,id'
        ]);

        $lang = $request->lang;
        $title = $request->title;
        $slug = Str::of($title)->slug('-');
        $get_content = $request->body_content;
        $featured_image = $request->file('featured_image');
        $featured_image_id = $request->featured_image_id;
        $status_id = $request->status_id;

        if ($featured_image) {
            $image_id = MediaUpload::uploadMedia($featured_image);
        } else if ($featured_image_id) {
            $image_id = $featured_image_id;
        } else {
            $image_id = null;
        }

        if ($get_content) {
            $content = $get_content;
        } else {
            $content = null;
        }

        $page = Page::where('id', $page_id)->first();
        if ($lang != null) {
            $page_translation = PageTranslation::where('page_id', $page_id)->where('lang', $lang)->first();
        } else {
            $page_translation = PageTranslation::where('page_id', $page_id)->first();
        }


        if ($page && $page_translation) {
            $page->update([
                'featured_image_id' => $image_id,
                'author_id' => Auth::user()->id,
            ]);


            $page_translation->update([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'status_id' => $status_id,
            ]);
        } else {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json(['message' => 'Page updated successfully', 'page' => $page]);

    }

    public function destroy($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->destroyPageFilter($param2, $param1);
        } else {
            return $this->destroyPageFilter($param1);
        }
    }

    public function destroyPageFilter($page_id, $lang = null)
    {
        if ($lang != null) {
            $page_translation = PageTranslation::where('page_id', $page_id)->where('lang', $lang)->first();
        } else {
            $page_translation = PageTranslation::where('page_id', $page_id)->first();
        }

        if ($page_translation) {
            $page_translation->delete();
            return response()->json(['message' => 'Page successfully moved to trash']);
        } else {
            return response()->json(['message' => 'Page not found'], 404);
        }

    }

    public function trash(Request $request, string $lang = null)
    {
        $per_page = $request->per_page;

        if ($lang != null) {
            $pages = PageTranslation::onlyTrashed()->where('lang', $lang)->paginate($per_page);
        } else {
            $pages = PageTranslation::onlyTrashed()->paginate($per_page);
        }
        if ($pages) {
            return response()->json($pages);
        } else {
            return response()->json(['message' => 'No pages have been trashed']);
        }

    }

    public function restore($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->restorePageFilter($param2, $param1);
        } else {
            return $this->restorePageFilter($param1);
        }
    }

    public function restorePageFilter($page_id, $lang = null)
    {
        if ($lang != null) {
            $page_translation = PageTranslation::onlyTrashed()->where('page_id', $page_id)->where('lang', $lang)->first();
        } else {
            $page_translation = PageTranslation::onlyTrashed()->where('page_id', $page_id)->first();
        }

        if ($page_translation) {
            $page_translation->restore();
            return response()->json(['page_id' => $page_id, 'message' => 'Page restored successfully']);
        } else {
            return response()->json(['message' => 'Page not found'], 404);
        }

    }

    public function delete($param1 = null, $param2 = null)
    {
        if (env('MULTILINGUAL') === true) {
            return $this->deletePageFilter($param2, $param1);
        } else {
            return $this->deletePageFilter($param1);
        }
    }

    public function deletePageFilter($page_id, $lang = null)
    {
        if ($lang != null) {
            $page_translation = PageTranslation::onlyTrashed()->where('page_id', $page_id)->where('lang', $lang)->first();
        } else {
            $page_translation = PageTranslation::onlyTrashed()->where('page_id', $page_id)->first();
        }

        if ($page_translation) {
            $page_translation->forceDelete();
            return response()->json(['message' => 'Page successfully deleted']);
        } else {
            return response()->json(['message' => 'Page not found'], 404);
        }
    }

}
