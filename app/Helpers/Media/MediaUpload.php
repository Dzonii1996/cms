<?php


namespace App\Helpers\Media;


use App\Models\Media;

class MediaUpload
{

    public  static  function  uploadMedia($media_file)
    {
        $properties=null;
        $media_ext=strolower ($media_file -> getClientOriginalExtension());
        $name_gen = hexdec(uniqid());
        if (in_array($media_ext, Media::AUDIO_EXTENSION)) {
            $up_location = 'media/audio/';
        } elseif (in_array($media_ext, Media::DOCUMENT_EXTENSION)) {
            $up_location = 'media/documents/';
        } elseif (in_array($media_ext, Media::IMAGE_EXTENSION)) {
            $up_location = 'media/images/';
        }

        $media_slug = $name_gen . '.' . $media_ext;

        $last_media = $up_location . $media_slug;
        $media_file->move($up_location, $media_slug);
        $alt = substr($media_file->getClientOriginalName(), 0, strpos($media_file->getClientOriginalName(), "."));

        if (in_array($media_ext, Media::IMAGE_EXTENSION)) {
            $thumbnail = Image::make($last_media);
            $medium = Image::make($last_media);

            $thumbnail->resize(170, 120, function ($constraint) {
                $constraint->aspectRatio();
            })->save($up_location . 'thumbnail-' . $media_slug);
            $thumbnail_location = $up_location . 'thumbnail-' . $media_slug;

            $medium->resize(450, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($up_location . 'medium-' . $media_slug);
            $medium_location = $up_location . 'medium-' . $media_slug;

            $properties = [
                'thumbnail' => $thumbnail_location,
                'medium' => $medium_location,
            ];
        }

        $media = Media::create([
            'alt' => $alt,
            'slug' => $last_media,
            'properties' => $properties,
            'type' => $media_ext,
        ]);

        if ($media) {
            return $media->id;
        } else {
            return response()->json(['message' => 'Media upload fails'], 500);
        }

    }

}
