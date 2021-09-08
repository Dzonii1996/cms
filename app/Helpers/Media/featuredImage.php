<?php


namespace App\Helpers\Media;


use App\Models\Media;

class featuredImage
{

    public static function uploadFeaturedImage($featured_image) {
        $name_gen = hexdec(uniqid());
        $image_ext = strtolower($featured_image->getClientOriginalExtension());
        $img_slug = $name_gen.'.'.$image_ext;
        $up_location = 'image/';
        $last_image = $up_location.$img_slug;
        $featured_image->move($up_location,$img_slug);

        $image = Media::create([
            'alt' => $featured_image->getClientOriginalName(),
            'slug' => $last_image,
            'type' => $image_ext,
        ]);

        return $image->id;
    }


}
