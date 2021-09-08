<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'featured_image_id',
        'author_id',
    ];

    public function featured_image()
    {
        return $this->hasOne(Media::class, 'id', 'featured_image_id');
    }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function post_content()
    {
        return $this->hasMany(PostTranslation::class, 'post_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_posts', 'post_id', 'category_id');
    }
}
