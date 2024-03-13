<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommentController;

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

// Route::middleware('auth:sanctum')->group(function () {
//     // Rutas protegidas que requieren autenticación
//     Route::post('createcommunity', [CommunityController::class, 'createCommunity'])->name('createcommunity');
//     Route::post('joincommunity', [CommunityController::class, 'joinCommunity'])->name('joincommunity');
// });

//USERS
Route::post('login', [UserController::class,'login'])->name('login');

Route::post('register', [UserController::class,'register'])->name('register');

// Rutas protegidas
Route::middleware('auth.sanctum')->group(function () {
    //USERS
    Route::patch('updateusername', [UserController::class,'updateUsername'])->name('updateusername');

    Route::patch('updateuseremail', [UserController::class,'updateUserEmail'])->name('updateuseremail');

    Route::patch('updateuserpassword', [UserController::class,'updateUserPassword'])->name('updateuserpassword');

    Route::patch('updateuserrole', [UserController::class,'updateUserRole'])->name('updateuserrole');

    // Route::patch('updateuseravatar', [UserController::class,'updateUserAvatar'])->name('updateuseravatar');  //

    Route::delete('deleteuser', [UserController::class,'deleteUser'])->name('deleteuser'); //

    //COMMUNITIES
    Route::post('createcommunity', [CommunityController::class,'createCommunity'])->name('createcommunity');

    Route::patch('updatecommunity', [CommunityController::class,'updateCommunity'])->name('updatecommunity');

    Route::patch('updatetitlecommunity', [CommunityController::class,'updateTitleCommunity'])->name('updatetitlecommunity');

    Route::patch('updatedescriptioncommunity', [CommunityController::class,'updateDescriptionCommunity'])->name('updatedescriptioncommunity');

    Route::patch('updatecountrycommunity', [CommunityController::class,'updateCountryCommunity'])->name('updatecountrycommunity');

    Route::patch('updatelanguagecommunity', [CommunityController::class,'updateLanguageCommunity'])->name('updatelanguagecommunity');

    Route::delete('deletecommunity', [CommunityController::class,'deleteCommunity'])->name('deletecommunity');

    //JOINCOMMUNITY
    Route::post('joincommunity', [CommunityController::class,'joinCommunity'])->name('joincommunity');

    Route::delete('leavecommunity', [CommunityController::class,'leaveCommunity'])->name('leavecommunity');

    //COMMENTS
    Route::post('createcomment', [CommentController::class,'createComment'])->name('createcomment');

    Route::delete('deletecomment', [CommentController::class,'deleteComment'])->name('deletecomment');
});
