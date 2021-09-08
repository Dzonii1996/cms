<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManuItemController extends Controller
{
    public function store(Request $request, int $menu_id)
    {
        $request->validate([
            'name' => 'required|unique:menus|max:255',
        ]);
    }
}
