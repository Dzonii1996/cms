<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    const IMAGE_EXTENSION = ['jpg', 'jpeg', 'png', 'webp'];
    const AUDIO_EXTENSION = ['mp3', 'mp4'];
    const DOCUMENT_EXTENSION = ['doc', 'docx', 'pdf'];

    protected $casts = [
        'porperties' => 'array',
    ];
    protected $fillable = [

        'alt',
        'slug',
        'properties',
        'type',


    ];
}
