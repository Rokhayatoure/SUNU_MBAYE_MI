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
        $user = Auth::guard('api')->user();
        $produit =Produit::find($produit_id);
        $panier=new Panier;
        $panier->email=$user->email;
        $panier->name=$user->name;
        $panier->prenom=$user->prenom;
        $panier->users_id=$user->users_id;
        $panier->contact=$user->contact;
       

        $panier->prix=$produit->prix * $request->quantiter;
        $panier->nom_produit=$produit->nom_produit;
        $panier->images=$produit->images;
        $panier->quantiter= $produit->quantiter;
        $panier->save();
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
