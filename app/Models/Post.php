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
        'featured_image_id',
        'content',
    ];

    public function featured_image()
    {
        return $this->hasOne(Media::class, 'id', 'featured_image_id');
    }
}
