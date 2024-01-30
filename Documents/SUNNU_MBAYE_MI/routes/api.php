<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DetailCommendeController;
use App\Http\Controllers\PagnerController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ProduitController;
use App\Models\DetailCommende;
use Spatie\LaravelIgnition\Solutions\SolutionProviders\RunningLaravelDuskInProductionProvider;

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
Route::post('inscription', [UserController::class ,'inscription']);
Route::post('login', [UserController::class ,'login']);
Route::post('logout', [UserController::class ,'logout']);
Route::put('/updateUser/{id}',[UserController::class ,'updateUser'] );
Route::get('/listeUser',[UserController::class ,'listeUser'] );
//anonce

Route::get('/listAnnonce', [AnnonceController::class ,'listAnnonce']);
Route::get('/voirPlus/{annonce_id}', [AnnonceController::class ,'voirPlus']);
//categorie

Route::get('/voiplusCategorie/{id}', [CategorieController::class ,'voiplusCategorie']);
Route::get('/listeCategorie', [CategorieController::class ,'listeCategorie']);
//produit

Route::get('Produitrecherche', [ProduitController::class ,'Produitrecherche']);
Route::get('rechercheProduit', [ProduitController::class ,'rechercheProduit']);
Route::get('listeProduit', [ProduitController::class ,'listeProduit']);

// Route::delete('/listeCategorie', [ProduitController::class ,'listeCategorie']);
//pagner
Route::get('/AfficheCommende', [DetailCommendeController::class, 'AfficheCommende']);
Route::get('/voirplus/{commende_id}', [DetailCommendeController::class, 'voirplus']);





Route::middleware('auth:api')->group( function (){
   
    // Route::post('/AjoutCategorie', [CategorieController::class ,'AjoutCategorie']);
    

    
});
Route::middleware(['auth','nom_role:revendeur'])->group(function () {
Route::POST('/AjoutPanier/{produit_id}', [PanierController::class, 'AjoutPanier']);
Route::get('/AfficherPanier', [PanierController::class, 'AfficherPanier']);
Route::delete('/viderPanier/{produit_id}', [PanierController::class, 'viderPanier']);
Route::delete('/validerPanier/{panier_id}', [PanierController::class, 'validerPanier']);
//commender 
Route::post('/effectuerCommande', [DetailCommendeController::class, 'effectuerCommande']);
Route::put('/AnnulerLivraison/{commende_id}', [DetailCommendeController::class, 'AnnulerLivraison']);


});


Route::middleware(['auth','nom_role:agriculteur'])->group(function () {
Route::post('AjoutProduit', [ProduitController::class ,'AjoutProduit']);
Route::put('updateproduit/{id}', [ProduitController::class ,'updateproduit']);
Route::delete('supProduit/{id}', [ProduitController::class ,'supProduit']);

});


Route::middleware(['auth','nom_role:admin'])->group(function () {
Route::post('/AjoutCategorie', [CategorieController::class ,'AjoutCategorie']);

Route::delete('/suprimmerCommende/{commende_id}', [DetailCommendeController::class, 'suprimmerCommende']);
Route::put('/LivraisonTerminer/{commende_id}', [DetailCommendeController::class, 'LivraisonTerminer']);
//annonce
Route::post('/ajoutAnnonce', [AnnonceController::class ,'ajoutAnnonce']);
Route::put('/modifierAnnonce/{id}',[AnnonceController::class ,'modifierAnnonce']);
Route::delete('/supAnnonce/{id}', [AnnonceController::class ,'supprimerAnnonce']);
//categorie
Route::put('/modifieCategorie/{id}', [CategorieController::class ,'modifieCategorie']);
Route::delete('/supCategorie/{id}', [CategorieController::class ,'destroy']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
