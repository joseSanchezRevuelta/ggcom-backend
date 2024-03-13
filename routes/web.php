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
Route::get('communities', [CommunityController::class,'getCommunities']);

Route::get('searchcommunities', [CommunityController::class,'getSearchCommunities']);

Route::get('community', [CommunityController::class,'getCommunity']);

Route::get('comments', [CommentController::class,'getComments']);

//USERS
Route::get('checkuser', [UserController::class,'checkUser']);

Route::get('searchusers', [UserController::class,'getSearchUsers']);

// Rutas protegidas
Route::middleware('auth.sanctum')->group(function () {
    //COMMUNNITIES
    Route::get('geteditcommunity', [CommunityController::class,'getEditCommunity']);

    //JOINCOMMUNITY
    Route::get('getjoincommunity', [CommunityController::class,'getJoinCommunity']);
    
    Route::get('myjoincommunities', [CommunityController::class,'getMyJoinCommunities']);
    
    Route::get('mycreatedcommunities', [CommunityController::class,'getCreatedCommunities']);

    //PROFILE
    Route::get('profile', [UserController::class,'profile']);

    //ADMIN
    Route::get('getusers', [UserController::class,'getUsers']);
});

