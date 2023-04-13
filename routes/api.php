<?php

use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RefLawukController;
use App\Http\Controllers\API\RefSayurController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Models\ProductCategory;
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


Route::get('products', [ProductController::class, 'all']);
Route::post('products', [ProductController::class, 'store']);
Route::post('products/{product}/update', [ProductController::class, 'update']);
Route::post('products/{product}', [ProductController::class, 'delete']);

Route::get('ref_sayur', [RefSayurController::class, 'all']);
Route::post('ref_sayur', [RefSayurController::class, 'store']);
Route::post('ref_sayur/{ref_sayur}/update', [RefSayurController::class, 'update']);
Route::post('ref_sayur/{ref_sayur}', [RefSayurController::class, 'delete']);

Route::get('ref_lawuk', [RefLawukController::class, 'all']);
Route::post('ref_lawuk', [RefLawukController::class, 'store']);
Route::post('ref_lawuk/{ref_lawuk}/update', [RefLawukController::class, 'update']);
Route::post('ref_lawuk/{ref_lawuk}', [RefLawukController::class, 'delete']);

Route::get('categories', [ProductCategoryController::class, 'all']);
Route::post('categories', [ProductCategoryController::class, 'store']);
Route::post('categories/{categories}/update', [ProductController::class, 'update']);
Route::post('categories/{categories}', [ProductController::class, 'delete']);

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function (){
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('transactions', [TransactionController::class, 'all']);
    Route::post('checkout', [TransactionController::class, 'checkout']);
    Route::post('products/{product}/vote', [ProductController::class, 'vote']);
});