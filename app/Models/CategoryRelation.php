<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRelation extends Model
{
    use HasFactory;


    public function allLangCategory(){
        return $this->hasMany(Category::class, 'main_category_id', 'id');
    }

    public function post_relation()
    {
        return $this->belongsToMany(PostRelation::class, 'categories_posts', 'category_id', 'post_id')
            ->with('post');
    }

    public function category()
    {
        return $this->hasMany(Category::class, 'main_category_id', 'id')->where('lang','=', 'sr');;
    }
}
