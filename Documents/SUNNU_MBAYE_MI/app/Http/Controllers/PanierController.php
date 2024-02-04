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
{/**
 * @OA\Post(
 *     path="/api/AjoutPanier/{produit_id}",
 *     summary="Ajoute un produit au panier",
 *     @OA\Parameter(
 *         name="produit_id",
 *         in="path",
 *         description="ID du produit à ajouter",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produit ajouté au panier avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="panier", type="object", ref="#/components/schemas/Panier")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Non connecté"),
 * )
 */

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
 * @OA\Get(
 *     path="/api/AfficherPanier",
 *     summary="Affiche le panier de l'utilisateur",
 *     @OA\Response(
 *         response=200,
 *         description="Panier affiché avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="panier", type="array", @OA\Items(ref="#/components/schemas/Panier"))
 *         )
 *     ),
 * )
 */
    //afficher pagner 
    public function AfficherPanier()
    {
        $id = Auth::guard('api')->user()->id;
        $panier = Panier::where('user_id', $id)->get();
        return response()->json(compact('panier'), 200);
    }




/**
 * @OA\Delete(
 *     path="/api/viderPanier/{produit_id}",
 *     summary="Vide un produit du panier",
 *     @OA\Parameter(
 *         name="produit_id",
 *         in="path",
 *         description="ID du produit à vider",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produit vidé du panier avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="panier", type="object", ref="#/components/schemas/Panier"),
 *             @OA\Property(property="message", type="string", example="Le produit est vidé du panier avec succès")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Produit non trouvé dans le panier"),
 * )
 */

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
