<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function menu_items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id')->where('parent_id', NULL)->with('child_items');    }
}
