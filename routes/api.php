<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;


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


//User endpoints
Route::post('/tokens/register', [AuthController::class,'register']);
Route::post('/tokens/login', [AuthController::class,'login']);
Route::get('/tokens/users', [AuthController::class,'index']);
Route::delete('/tokens/delete/{id}', [AuthController::class,'destroy']);
Route::put('/tokens/update/{id}', [AuthController::class,'update']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tokens/logout', [AuthController::class,'logout']);
});



//Category
Route::post('/category/store', [CategoryController::class,'store']);
Route::get('/category/show', [CategoryController::class,'index']);
Route::put('/category/update/{id}', [CategoryController::class,'update']);
Route::delete('/category/delete/{id}', [CategoryController::class,'destroy']);

//Products endpoints
Route::post('/product/store', [ProductController::class,'store']);
Route::get('/product/show', [ProductController::class,'index']);
Route::delete('/product/delete/{id}', [ProductController::class,'destroy']);
Route::put('/product/update/{id}', [ProductController::class,'update']);
Route::get('/product/search/{name}', [ProductController::class,'search']);

//Order endpoints
Route::get('/orders/list', [OrderController::class, 'index']);
Route::post('/orders/store', [OrderController::class, 'store']);
