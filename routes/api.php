<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ProductController;
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

Route::post('register', [AuthController::class, 'register'])->name('user.register');
Route::post('login', [AuthController::class, 'login'])->name('user.login');

// Auth Routes...
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Grouping post-related routes
    Route::prefix('post')->group(function () {
        Route::get('list', [PostController::class, 'list']);
        Route::post('store', [PostController::class, 'store']);
        Route::post('comment/{id}', [PostController::class, 'commentPost']);
        Route::post('like/{id}', [PostController::class, 'likePost']);
    });
});

Route::middleware('auth:sanctum')->get('/userss', function (Request $request) {
    return response()->json([
        'user_info' => $request->user(),
    ]);
});

Route::get('products', [ProductController::class, 'product_list']);
