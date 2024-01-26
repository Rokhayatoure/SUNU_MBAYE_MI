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

        $panier = panier::where('user_id',auth()->guard('api')->user()->id)->get();
    
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
   
   }
   $commende->quantite =$cptQ;
   $commende->prix =$cptC;

    // Supprimez tous les articles du panier de l'utilisateur après la création de la commande
    panier::where('user_id', $user->id)->delete();
    $commende->save();
        }


   public function AfficheCommende ()
    {
        $id = Auth::guard('api')->user()->id;
        $commende = Commende::where('user_id', $id)->get();
        return response()->json(compact('commende'), 200);
    }



    public function voirPlus( $commende_id)
    {
        if (Auth::guard('api')->check())
        {
            $commende = Commende::find($commende_id);
            return response()->json(compact('commende'), 200);
    

        }
        else{
            return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
        }
    }
    public function suprimmerCommende($commende_id)
    {
        if(!Auth::guard('api')->check()){
            return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);

        }
        Commende::find($commende_id)->delete();
        return response()->json(['message' => 'commende supprimé avec succès'], 200);
    }
    }


