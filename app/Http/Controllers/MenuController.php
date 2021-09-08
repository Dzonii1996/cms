<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index() {
        $menus = Menu::with('menu_items')->get();

        return response()->json($menus);
    }
public function store(Request $request) {
    $request->validate([
        'name' => 'required|unique:menus|max:255',
    ]);

    $menu = Menu::create([
        'name' => $request->name,
    ]);

    return response()->json($menu);
}
    public function update(Request $request,int $menu_id){
        $menu = Menu::find($menu_id)->update([
            'name' => $request->name,
        ]);

        return response()->json($menu);
    }

    public function destroy(int $menu_id) {
        Menu::findOrFail($menu_id)->forceDelete();
        return response()->json(['message' => 'Menu successfully deleted!']);
    }

    public function show(int $menu_id) {
        $menu = MenuItem::where(['menu_id'=> $menu_id])->with('menu_items')->get();

        return response()->json($menu);
    }

}
