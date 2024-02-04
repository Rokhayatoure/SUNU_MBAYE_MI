<?php

use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagnerController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\AnnoncepublierController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\PayementController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommendeController;
use App\Http\Controllers\MessageController;
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
Route::post('ajouterMessage', [MessageController::class ,'ajouterMessage']);

// Route::delete('/listeCategorie', [ProduitController::class ,'listeCategorie']);
//pagner





//revendeur middleware
Route::middleware(['auth','nom_role:revendeur'])->group(function () {
Route::POST('/AjoutPanier/{produit_id}', [PanierController::class, 'AjoutPanier']);
Route::get('/AfficherPanier', [PanierController::class, 'AfficherPanier']);
Route::delete('/viderPanier/{produit_id}', [PanierController::class, 'viderPanier']);
Route::delete('/validerPanier/{panier_id}', [PanierController::class, 'validerPanier']);
//commender 
Route::put('/AnnulerLivraison/{commende_id}', [CommendeController::class, 'AnnulerLivraison']);
Route::post('/Commander', [CommendeController::class, 'Commander']);

});

//agriculteur middleware
Route::middleware(['auth','nom_role:agriculteur'])->group(function () {
Route::put('updateproduit/{id}', [ProduitController::class ,'updateproduit']);
Route::delete('supProduit/{id}', [ProduitController::class ,'supProduit']);
Route::get('/listeAnnonceAgriculteur', [AnnonceController::class ,'listeAnnonceAgriculteur']);
Route::post('AjoutProduit', [ProduitController::class ,'AjoutProduit']);
Route::get('listeProduitAgriculteur', [ProduitController::class ,'listeProduitAgriculteur']);

Route::post('/ajoutAnnonce', [AnnonceController::class ,'ajoutAnnonce']);




});

//admin middleware
Route::middleware(['auth','nom_role:admin'])->group(function () {
Route::post('/AjoutCategorie', [CategorieController::class ,'AjoutCategorie']);

Route::delete('/suprimmerCommende/{commende_id}', [CommendeController::class, 'suprimmerCommende']);
Route::put('/LivraisonTerminer/{commende_id}', [CommendeController::class, 'LivraisonTerminer']);
//annonce
Route::put('/modifierAnnonce/{id}',[AnnonceController::class ,'modifierAnnonce']);
Route::delete('/supAnnonce/{id}', [AnnonceController::class ,'supprimerAnnonce']);
//categorie
Route::put('/modifieCategorie/{id}', [CategorieController::class ,'modifieCategorie']);
Route::delete('/supCategorie/{id}', [CategorieController::class ,'destroy']);
Route::get('/listeUser',[UserController::class ,'listeUser'] );
Route::get('/AfficheCommende', [CommendeController::class, 'AfficheCommende']);
Route::get('/voirplus/{commende_id}', [CommendeController::class, 'voirplus']);

Route::get('/publierAnnonce/{id}', [AnnonceController::class ,'publierAnnonce']);
Route::get('/retirerAnnonce/{id}', [AnnonceController::class ,'retirerAnnonce']);
Route::get('listerMessages', [MessageController::class ,'listerMessages']);

});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('payment', [PayementController::class, 'index'])->name('payment.index');
Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');
Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');
// Route::post('initiatePayment/{commende_id}', [PayementController::class, 'initiatePayment']);
// Route::post('payment', [PayementController::class, 'payment']);

