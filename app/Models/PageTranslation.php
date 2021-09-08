<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'lang',
        'title',
        'slug',
        'content',
        'status_id',
    ];

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }
}
