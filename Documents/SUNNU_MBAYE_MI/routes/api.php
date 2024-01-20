<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CategorieController;

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
//Utilisateur inscription auth sup deconnection
Route::post('/role', [UserController::class ,'ajouterRole']);
Route::post('inscrirption', [UserController::class ,'inscription']);
Route::post('login', [UserController::class ,'login']);
Route::post('logout', [UserController::class ,'logout']);
Route::put('/utilisateur/{id}',[UserController::class ,'updateUser'] );
//anonce
Route::post('/ajoutAnnonce', [AnnonceController::class ,'ajoutAnnonce']);
Route::put('/modifiAnnonce/{id}', [AnnonceController::class ,'modifieAnnonce']);
Route::delete('/supAnnonce/{id}', [AnnonceController::class ,'supAnnonce']);
//categorie
Route::get('listeCategorie', [CategorieController::class ,'listeCategorie']);
Route::post('AjoutCategorie', [CategorieController::class ,'AjoutCategorie']);
Route::put('modifieCategorie/{id}', [CategorieController::class ,'modifieCategorie']);
Route::delete('supCategorie/{id}', [CategorieController::class ,'supCategorie']);
//produit



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
