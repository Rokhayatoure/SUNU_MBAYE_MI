<?php

namespace App\Http\Controllers;

use App\Models\panier;
use App\Models\Produit;
use App\Models\Commende;
use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DetailCommendeController extends Controller
{
    public function effectuerCommande()
    {
        // Récupérez le produit par son ID
        if (!Auth::guard('api')->check()) {
            return response()->json(['errors' => 'veilleir vous connecter avant de fair cette action.'], 422);
        }
        $user = Auth::guard('api')->user();
    // dd($user);
        $panier = panier::where('user_id',auth()->guard('api')->user()->id)->get();
    
        // if ( $panier->isEmpty()) 
        // {
        //     return response()->json(['message' => ' votre panier est vide'], 201);
        // }
        $commende = new Commende();
        $commende->livraison = 'En_court';
        $commende->user_id= auth()->guard('api')->user()->id;
        $commende->nom=$user->nom;
        $commende->prenom =$user->prenom;
        $cptQ = 0;
        $cptC = 0;
      // Ajoutez chaque article du panier à la table de commande produit
      foreach( $panier as $item) {
       $cptQ+= $item->quantite;
       $cptC+=$item->prix;
    //    $commende->email= $item->email;
   }
   $commende->quantite =$cptQ;
   $commende->prix =$cptC;
//    dd($panier);
    // Supprimez tous les articles du panier de l'utilisateur après la création de la commande
    panier::where('user_id', $user->id)->delete();
    $commende->save();
        
       

}
private function Calculepanier($panier)
{
    $total = 0;

    foreach ($panier as $item) {
        $total +=   $item->prix* $item->quantite;
    }

    return $total;
}

}
