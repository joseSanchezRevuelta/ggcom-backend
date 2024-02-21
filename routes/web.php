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

Route::get('communities', [CommunityController::class,'getCommunities']);   //

Route::get('mycommunities', [CommunityController::class,'getMyCommunities']);   //Hay que protegerla crear middleware para enviar respuesta personalizada porque si no laravel buscara la ruta login si no estás autentificado

Route::middleware('auth.sanctum')->group(function () {
    Route::get('mycommunities', [CommunityController::class,'getMyCommunities']);

    // Route::get('/ruta-protegida-2', function (Request $request) {
    //     return $request->user();
    // });

    // Puedes agregar más rutas protegidas dentro de este grupo
});


// Route::get('communities/language', [CommunityController::class, 'getCommunitiesLanguage']);  //Language

