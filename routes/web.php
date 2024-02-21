<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommunityController;

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

Route::get('communities', [CommunityController::class,'getCommunities']);    //Popular

Route::get('communities/language', [CommunityController::class, 'getCommunitiesLanguage']);  //Language

