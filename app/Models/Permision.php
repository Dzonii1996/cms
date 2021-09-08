<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permision extends Model
{
    use HasFactory;

    public function roles() {

        return $this->belongsToMany(Role::class,'roles_permissions');

    }

}
