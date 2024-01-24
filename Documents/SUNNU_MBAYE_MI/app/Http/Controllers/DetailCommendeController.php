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
    public function effectuerCommande(Request $request ,Produit $produit_id)
    {
        // Récupérez le produit par son ID
        $user = Auth::guard('api')->user();
        $panier = panier::where('user_id', $user->id)->get();
        if ( $panier->isEmpty()) 
        {
            return response()->json(['message' => ' votre panier est vide'], 201);
        }
        $commende = new Commende();
        $commende->livraison = 'En_cours';
        $commende->users_id= $user->id;
        $commende->name =$user->name;
        $commende->prenom =$user->prenom;
        $commende->date_commende =Carbon::now()->format('d/m/Y');
        // Ajoutez chaque article du panier à la table de commande produit
        foreach( $panier as $item) {
            $commendeProduit = new DetailCommende();
            $commendeProduit->commende_id = $commende->id;
            $commendeProduit->produit_id = $item->product_id;
            // $commendeProduit->vendor_id = $item->vendor_id; // vous pouvez ajouter l'ID du vendeur si vous avez plusieurs vendeurs
            $commendeProduit->color = $item->color;
            $commendeProduit->size = $item->size;
            $commendeProduit->quantiter= $item->quantiter;
            $commendeProduit->prix= $item->prix;
            $commendeProduit->save();

    }
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
