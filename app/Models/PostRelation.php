<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRelation extends Model
{
    use HasFactory;



    public function post()
    {
        return $this->hasMany(Post::class, 'main_post_id', 'id');
    }
}
