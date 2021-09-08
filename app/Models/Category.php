<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_category_id',
        'name',
        'slug',
        'parent_id',
    ];

    public function posts(){
        return $this->belongsToMany(Post::class, 'categories_posts', 'category_id','post_id');
    }


}
