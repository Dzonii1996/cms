<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'name',
        'link',
        'type',

    ];
    public function child_items(){
        return $this->hasMany(MenuItem::class, 'parent_id', 'id');
    }
}
