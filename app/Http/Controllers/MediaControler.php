<?php


namespace App\Http\Controllers;


use App\Helpers\Media\MediaUpload;
use App\Models\Media;
use http\Env\Request;

class MediaControler extends  Controller
{
    public  function  index (Request  $request)
    {
        $per_page=$request->per_page;
        $media =Media::paginate($per_page);
        return response()->json($media);

    }
    public  function  store (Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,mp3,mp4,doc,docx,pdf',
        ]);
        $media_file = $request->file('file');

        $media = MediaUpload::uploadMedia($media_file);

        if ($media) {
            return response()->json(['message' => 'Media upload successfully']);
        } else {
            return response()->json(['message' => 'Media upload failed'], 422);
        }

    }
    public  function  update (Request  $request, int $media_id) {
        $media_alt = $request->alt;
        $media = Media::where('id', $media_id)->first();

        if ($media) {
            $media->update([
                'alt' => $media_alt
            ]);
            return response()->json(['message' => 'Media updated successfully']);
        } else {
            return response()->json(['error' => 'Media not found'], 404);
        }

    }


    public function show(int $media_id)
    {
        $media = Media::findOrFail($media_id);

        return response()->json($media);
    }

    public function images(Request $request)
    {
        $image_ext = ['jpg', 'jpeg', 'png'];
        $per_page = $request->per_page;

        $media = Media::whereIn('type', $image_ext)->paginate($per_page);

        return response()->json($media);
    }

    public function documents(Request $request)
    {
        $document_ext = ['doc', 'docx', 'pdf'];
        $per_page = $request->per_page;

        $media = Media::whereIn('type', $document_ext)->paginate($per_page);

        return response()->json($media);
    }

    public function audio(Request $request)
    {
        $audio_ext = ['mp3', 'mp4'];
        $per_page = $request->per_page;

        $media = Media::whereIn('type', $audio_ext)->paginate($per_page);

        return response()->json($media);
    }

    public function destroy(int $media_id)
    {
        $media = Media::where('id', $media_id)->first();

        if ($media) {
            $media->delete();
            return response()->json(['message' => 'Media deleted successfully']);
        } else {
            return response()->json(['error' => 'Media not found'], 404);
        }
    }




}
