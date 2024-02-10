<?php

namespace App\Http\Controllers;

use App\Models\Commende;
use App\Models\panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class PanierController extends Controller
{

    public function AjoutPanier(Request $request,$produit_id)
    {
        if (Auth::guard('api')->check()){

      
        $user = Auth::guard('api')->user();
        $produit =Produit::find($produit_id);
        $panier=new Commende();
        $panier->email=$user->email;
        $panier->nom=$user->nom;
        $panier->prenom=$user->prenom;
        $panier->user_id=auth()->guard('api')->user()->id;
        $panier->quantite= $produit->quantite;
        $panier->prix=intval($produit->prix )*intval($produit->quantite);
        $panier->nom_produit=$produit->nom_produit;
        $panier->images=$produit->images;
        $panier->produit_id= $produit->id;
        


        $panier->save(); 
    }   else{
        return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
    }
        return response()->json([
            'status' => true,
            'panier' => $panier
        ], 201);
        }

    

    //afficher pagner 
    public function AfficherPanier()
    {
        $id = Auth::guard('api')->user()->id;
        $panier = Panier::where('user_id', $id)->get();
        return response()->json(compact('panier'), 200);
    }






    public function viderPanier($produit_id)
    {

        $panier= Panier::find($produit_id);
       
        if (! $panier= Panier::find($produit_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Produit non trouvé dans le panier.'
            ], 404);
        }
    
    
     $panier->delete();
     return response()->json([
                'status' => true,
                 'panier'=>$panier,
                 'message'=>'le produit est vider du panier avec success'
            ], 201);

    }




    // function validerPanier($panier_id) {
    //     // Vérifier si le panier est vide
    //     if(count($panier_id->produit) == 0) {
    //         return response()->json(['message' => 'Votre panier est vide'], 400);
    //     }
    
    //     // Calculer le total du panier
    //     $total = 0;
    //     foreach($panier_id->produit as $produit) {
    //         $total += $produit->prix * $produit->quantite;
    //     }
    
    //     // Vérifier si le total du panier est supérieur à zéro
    //     if($total <= 0) {
    //         return response()->json(['message' => 'Le total du panier doit être supérieur à zéro'], 400);
    //     }
    
    //     // Si tout est correct, valider le panier
    //     $panier->valide = true;
    //     $panier->save();
    
    //     return response()->json(['message' => 'Panier validé avec succès'], 200);
    // }
    
}
