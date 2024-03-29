<?php

use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagnerController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommendeController;
use App\Http\Controllers\PayementController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\AnnoncepublierController;
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
// Route::get('/greeting/{locale}', function (string $locale) {
//     if (! in_array($locale, [ 'fr'])) {
//         abort(400);
//     }
 
//     App::setLocale($locale);
 
//     // ...
// });
Route::get('/listeUser',[UserController::class ,'listeUser'] );


Route::post('inscription', [UserController::class ,'inscription']);
Route::post('login', [UserController::class ,'login']);
Route::post('logout', [UserController::class ,'logout']);
Route::post('/updateUser/{id}',[UserController::class ,'updateUser'] );

//anonce

Route::get('/voirPlus/{annonce_id}', [AnnonceController::class ,'voirPlus']);
//categorie

Route::get('/voiplusCategorie/{id}', [CategorieController::class ,'voiplusCategorie']);
Route::get('/listeCategorie', [CategorieController::class ,'listeCategorie']);
//produit
Route::get('Produitrecherche', [ProduitController::class ,'Produitrecherche']);
Route::get('filtrerProduitsParCategorie', [ProduitController::class ,'filtrerProduitsParCategorie']);
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
Route::post('/Commender', [CommendeController::class, 'Commender']);
Route::get('/VoirplusCommendeRevendeur/{commendeId}', [CommendeController::class, 'VoirplusCommendeRevendeur']);

});


//agriculteur middleware
Route::middleware(['auth','nom_role:agriculteur'])->group(function () {
Route::post('updateproduit/{id}', [ProduitController::class ,'updateproduit']);
Route::delete('supProduit/{id}', [ProduitController::class ,'supProduit']);
Route::get('/listeAnnonceAgriculteur', [AnnonceController::class ,'listeAnnonceAgriculteur']);
Route::post('AjoutProduit', [ProduitController::class ,'AjoutProduit']);
Route::get('listeProduitAgriculteur', [ProduitController::class ,'listeProduitAgriculteur']);
Route::post('/ajoutAnnonce', [AnnonceController::class ,'ajoutAnnonce']);
Route::delete('/supAnnonce/{id}', [AnnonceController::class ,'supprimerAnnonce']);
Route::get('/VoirplusCommendeAgriculteur/{commendeId}', [CommendeController::class, 'VoirplusCommende']);
});

//admin middleware
Route::middleware(['auth','nom_role:admin'])->group(function () {
Route::post('/AjoutCategorie', [CategorieController::class ,'AjoutCategorie']);

Route::delete('/suprimmerCommende/{commende_id}', [CommendeController::class, 'suprimmerCommende']);
Route::put('/LivraisonTerminer/{commende_id}', [CommendeController::class, 'LivraisonTerminer']);
Route::put('/AnnulerLivraison/{commende_id}', [CommendeController::class, 'AnnulerLivraison']);

//annonce
Route::post('/modifierAnnonce/{id}',[AnnonceController::class ,'modifierAnnonce']);
//categorie
Route::put('/modifieCategorie/{id}', [CategorieController::class ,'modifieCategorie']);
Route::delete('/supCategorie/{id}', [CategorieController::class ,'destroy']);
Route::get('/publierAnnonce/{id}', [AnnonceController::class ,'publierAnnonce']);
Route::get('/retirerAnnonce/{id}', [AnnonceController::class ,'retirerAnnonce']);
Route::get('listerMessages', [MessageController::class ,'listerMessages']);
Route::get('/listeAnnoncesPubliees', [AnnonceController::class ,'listeAnnoncesPubliees']);
Route::delete('supProduitAdmine/{id}', [ProduitController::class ,'supProduitAdmine']);
// user
Route::delete('debloquerUser/{id}', [UserController::class ,'debloquerUser']);
Route::delete('BloquerUser/{id}', [UserController::class ,'BloquerUser']);


//role
Route::post('/role', [UserController::class ,'ajouterRole']);
Route::get('/listRole', [UserController::class ,'listRole']);

Route::get('/listeCommandes', [CommendeController::class, 'listeCommandes']);
Route::get('/VoirplusCommende/{commendeId}', [CommendeController::class, 'VoirplusCommende']);
Route::post('reponse', [MessageController::class ,'reponse']);
Route::get('voirplusmessage/{id}', [MessageController::class ,'voirplusmessage']);
Route::get('/listAnnonce', [AnnonceController::class ,'listAnnonce']);


});


Route::get('payment', [PayementController::class, 'index'])->name('payment.index');
Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');
Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');
