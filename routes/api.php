<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommunitiesController;
use App\Http\Controllers\CommentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->group(function () {
//     // Rutas protegidas que requieren autenticación
//     Route::post('createcommunity', [CommunitiesController::class, 'createCommunity'])->name('createcommunity');
//     Route::post('joincommunity', [CommunitiesController::class, 'joinCommunity'])->name('joincommunity');
// });

Route::post('login', [LoginController::class,'login'])->name('login');

Route::post('register', [LoginController::class,'register'])->name('register');

Route::post('createcommunity', [CommunitiesController::class,'createCommunity'])->name('createcommunity');

Route::post('joincommunity', [CommunitiesController::class,'joinCommunity'])->name('joincommunity');

Route::post('leavecommunity', [CommunitiesController::class,'leaveCommunity'])->name('leavecommunity');

Route::post('deletecommunity', [CommunitiesController::class,'deleteCommunity'])->name('deletecommunity');

Route::post('updatecommunity', [CommunitiesController::class,'updateCommunity'])->name('updatecommunity');

Route::post('createcomment', [CommentsController::class,'createComment'])->name('createcomment');

Route::post('deletecomment', [CommentsController::class,'deleteComment'])->name('deletecomment');

