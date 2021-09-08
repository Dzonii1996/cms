<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTranslation extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable =[
        'post_id',
        'lang',
        'title',
        'slug',
        'content',
        'audio_id',
        'status_id',
    ];

    public  function  status ()
    {
        return $this->hasOne(Status::class,'id','status_id');
    }
}
