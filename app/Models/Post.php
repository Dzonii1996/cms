<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_post_id',
        'title',
        'slug',
        'slug',
        'lang',
        'featured_image_id',
        'content',
    ];
    public function  featured_image(){
        return $this->hasOne(Media::class, 'id', 'featured_image_id');
    }

    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }
    public function author(){
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function category(){
        return $this->belongsToMany(Category::class, 'categories_posts', 'post_id', 'category_id' );
    }

}
