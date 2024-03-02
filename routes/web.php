<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//COMMUNITIES
Route::get('communities', [CommunityController::class,'getCommunities']);   //

Route::get('community', [CommunityController::class,'getCommunity']);   //

Route::get('comments', [CommentController::class,'getComments']);   //

// Rutas protegidas
Route::middleware('auth.sanctum')->group(function () {
    //COMMUNNITIES
    Route::get('mycommunities', [CommunityController::class,'getMyCommunities']);
    //JOINCOMMUNITY
    Route::get('getjoincommunities', [CommunityController::class,'getJoinCommunities']);

    Route::get('getjoincommunity', [CommunityController::class,'getJoinCommunity']);
    //PROFILE
    Route::get('profile', [UserController::class,'profile']);
});

