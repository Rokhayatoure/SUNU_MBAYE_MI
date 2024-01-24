<?php

namespace App\Http\Controllers;

use App\Models\panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class PanierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function AjoutPanier(Request $request,$produit_id)
    {
        if (Auth::guard('api')->check()){

      
        $user = Auth::guard('api')->user();
        $produit =Produit::find($produit_id);
        $panier=new Panier;
        $panier->email=$user->email;
        $panier->nom=$user->nom;
        $panier->prenom=$user->prenom;
        $panier->user_id=auth()->guard('api')->user()->id;
        $panier->contact=$user->contact;
        $panier->prix=$user->prix;
        $panier->quantite= $produit->quantite;
        $panier->prix=$produit->prix * $request->quantite;
        $panier->nom_produit=$produit->nom_produit;
        $panier->images=$produit->images;
      
        $panier->produit_id= $produit->id;
        // dd($panier);


        $panier->save(); 
    }   else{
        return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
    }
        return response()->json([
            'status' => true,
            'panier' => $panier
        ], 201);
        }

    /**
     * Show the form for creating a new resource.
     */
    //afficher pagner 
    public function AfficherPanier()
    {
        $id = Auth::guard('api')->user()->id;
        $panier = Panier::where('users_id', $id)->get();
        return response()->json(compact('panier'), 200);
    }

    public function suprimmerPanier($id)
    {
    $panier= Panier::find($id);
     $panier->delete();
    }

}
