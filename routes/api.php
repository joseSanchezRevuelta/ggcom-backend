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

//USERS
Route::post('login', [LoginController::class,'login'])->name('login');

Route::post('register', [LoginController::class,'register'])->name('register');

Route::patch('updateuser', [LoginController::class,'updateUser'])->name('updateuser');

Route::delete('deleteuser', [LoginController::class,'deleteUser'])->name('deleteuser');

//COMMUNITIES
Route::post('createcommunity', [CommunitiesController::class,'createCommunity'])->name('createcommunity');

Route::post('joincommunity', [CommunitiesController::class,'joinCommunity'])->name('joincommunity');

Route::patch('updatecommunity', [CommunitiesController::class,'updateCommunity'])->name('updatecommunity');

Route::delete('leavecommunity', [CommunitiesController::class,'leaveCommunity'])->name('leavecommunity');

Route::delete('deletecommunity', [CommunitiesController::class,'deleteCommunity'])->name('deletecommunity');

//COMMENTS
Route::post('createcomment', [CommentsController::class,'createComment'])->name('createcomment');

Route::delete('deletecomment', [CommentsController::class,'deleteComment'])->name('deletecomment');

