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
     * @OA\Get(
     *     path="/api/produits",
     *     summary="Liste des produits",
     *     @OA\Response(response=200, description="Renvoie la liste de tous les produits.")
     * )
     */
    public function listeProduit()
    {
        
        $produit=Produit::all();
        return response()->json(compact('produit'), 200);
    }
   /**
 * @OA\Post(
 *     path="/api/AjoutProduit",
 *     summary="Ajoute un nouveau produit",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="nom_produit",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="quantite",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="prix",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="categorie_id",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="images",
 *                     type="file"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produit ajouté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="produit", type="object", ref="#/components/schemas/Produit")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=422, description="Erreurs de validation")
 * )
 */
    
    public function AjoutProduit(Request $request)
    {
      
        
        if(Auth::guard('api')->check())
        {
     
        
            $user = Auth::guard('api')->user();
            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->quantite = intval($request->quantite);
            $produit->prix = intval($request->prix);
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


/**
 * @OA\Put(
 *     path="/api/updateproduit/{id}",
 *     summary="Mettre à jour un produit existant",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du produit à mettre à jour",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom_produit", type="string"),
 *                 @OA\Property(property="quantite", type="integer"),
 *                 @OA\Property(property="prix", type="integer"),
 *                 @OA\Property(property="categorie_id", type="integer"),
 *                 @OA\Property(property="images", type="file")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produit mis à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="produit", type="object", ref="#/components/schemas/Produit")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=403, description="Interdit d'accès"),
 *     @OA\Response(response=404, description="Produit non trouvé"),
 *     @OA\Response(response=422, description="Erreurs de validation")
 * )
 */

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
        $produit->quantite = intval($request->quantite);
        $produit->prix = intval($request->prix);
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

    


/**
     * @OA\Delete(
     *     path="/api/supProduit/{id}",
     *     summary="Supprime un produit",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Produit supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Interdit d'accès"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */


    public function supProduit($id)
    {
        
        if(!Auth::guard('api')->check()){
            return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);

        }
        Produit::find($id)->delete();
        return response()->json(['message' => 'produit supprimé avec succès'], 200);
    }





 
}
