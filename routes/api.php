<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ManuItemController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//POST
Route::group([
    'middleware' => 'api',
    'prefix' => '{lang}'
], function () {

    Route::post('posts/{main_post?}', [PostController::class, 'store']);
    Route::get('posts/{main_page}', [PageController::class, 'show']);
    Route::get('posts/trash', [PostController::class, 'trash']);
    Route::post('posts/trash/{page}', [PostController::class, 'restore']);
    Route::delete('posts/trash/{page}', [PostController::class, 'delete']);

    Route::resource('posts', PostController::class)->except([
        'store', 'show'
    ]);
//CATEGORY
    Route::get('posts', [PostController::class, 'allLangPosts']);
    Route::group([
        'middleware' => 'api',
        'prefix' => '{lang}'
    ], function () {

        Route::post('category/{main_category?}', [CategoryController::class, 'store']);
        Route::get('category/{main_category}', [CategoryController::class, 'show']);

        Route::resource('category', CategoryController::class)->except([
            'store', 'show'
        ]);
    });
    Route::get('category', [CategoryController::class, 'allLangCategory']);

    //MENU
    Route::group([
        'middleware' => 'api',
        'prefix' => '{lang}'
    ], function () {
        Route::get('menu{menu}', [MenuController::class, 'show']);
        Route::post('menu{menu}', [ManuItemController::class, 'store']);
    });
    Route::resource('menu', MenuController::class)->except([
        'show'
    ]);
    //PAGE

    Route::group([
        'middelwere' => 'api',
        'prefix' => '{lang}'
    ], function () {


        Route::post('pages/{main_page?}', [PageController::class, 'store']);
        Route::get('pages/{main_page}', [PageController::class, 'show']);
        Route::get('pages/trash', [PageController::class, 'trash']);
        Route::post('pages/trash/{page}', [PageController::class, 'restore']);
        Route::delete('pages/trash/{page}', [PageController::class, 'delete']);


        Route::resource('pages', PageController::class)->except([
            'store', 'show'
        ]);
    });
    Route::get('pages', [PageController::class, 'allLangPages']);


    Route::group([

        'middleware' => 'api',
        'prefix' => 'auth'
    ], function () {

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);

    });
});
