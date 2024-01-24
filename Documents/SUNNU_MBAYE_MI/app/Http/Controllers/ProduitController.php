<?php

namespace App\Http\Controllers;
session_start();
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listeProduit()
    {
        
        $produit=Produit::all();
        return response()->json(compact('produit'), 200);
    }
   
    /**
     * Show the form for creating a new resource.
     */
    public function AjoutProduit(Request $request)
    {
      
        
        if(Auth::guard('api')->check())
        {
     
        
            $user = Auth::guard('api')->user();
            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->quantite = $request->quantite;
            $produit->prix = $request->prix;
            $produit->user_id = $user->id; // Récupérer l'id de l'utilisateur connecté
            $produit->categorie_id = $request->categorie_id;
          
         
    
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $produit->images = $filename;
            }
           
            $produit->save();
        }
        else{
            return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
        }
        // Renvoyer une réponse JSON avec le produit créé et un code de statut 201
        return response()->json([
            'status' => true,
            'produit' => $produit
        ], 201);
    
    }

    public function updateproduit(Request $request,$id)
    {
        //  dd('ok');
        $user = Auth::guard('api')->user();
        $produit = Produit::find($id);
         if (!$produit) {
                return response()->json(['message' => 'Produit  non trouvée'], 404);
            }
    
            // Vérifier si l'utilisateur est le propriétaire de l'annonce
            if ($produit->users_id !== $user->id) {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette annonce'], 403);
            }
        $produit->nom_produit = $request->nom_produit;
        $produit->quantiter = $request->quantiter;
        $produit->prix = $request->prix;
        $produit->user_id = auth()->id(); // Récupérer l'id de l'utilisateur connecté
        $produit->categorie_id = $request->categorie_id;
        if ($request->hasFile('images')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $produit->image = $filename;
        }
        $produit->save();
    
        // Renvoyer une réponse JSON avec le produit créé et un code de statut 201
        return response()->json([
            'status' => true,
            'produit' => $produit
        ], 201);
    }

    
    public function supProduit($id)
    {
        
        if(!Auth::guard('api')->check()){
            return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);

        }
        Produit::find($id)->delete();
        return response()->json(['message' => 'produit supprimé avec succès'], 200);
    }





 
}
