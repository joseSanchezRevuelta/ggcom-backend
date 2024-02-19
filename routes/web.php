<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommunitiesController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('ejemplo', function () {
//     return "<h1>HOLA</h1>";
// });

Route::get('getcommunities', [CommunitiesController::class,'getCommunities']);

// Route::post('register', [LoginController::class,'register'])->name('register');