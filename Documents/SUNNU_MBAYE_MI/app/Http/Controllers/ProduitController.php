<?php

namespace App\Http\Controllers;
session_start();
use Exception;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ProduitController extends Controller
{
 /**
     * @OA\Get(
     *     path="/api/listeProduitAgriculteur",
     *     summary="Liste des produits  de L'agriculteur ",
     *     @OA\Response(response=200, description="Renvoie la liste de tous les produits.")
     * )
     */
    public function listeProduitAgriculteur()
    {
        
        
     $produit = Produit::where('user_id',auth()->guard('api')->user()->id)->get();
        return response()->json(compact('produit'), 200);
    }

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
        // $panier = Produit::where('user_id',auth()->guard('api')->user()->id)->get();
        return response()->json(compact('produit'), 200);
    }
/**
 * @OA\Post(
 *     path="/api/ajoutProduit",
 *     summary="Ajouter un produit",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom_produit", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="quantite", type="integer"),
 *                 @OA\Property(property="prix", type="integer"),
 *                 @OA\Property(property="categorie_id", type="integer"),
 *                 @OA\Property(property="images", type="string", format="binary")
 *             )
 *         )
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=201,
 *         description="Produit ajouté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="produit", type="object", ref="#/components/schemas/Produit")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé - Jeton invalide"),
 *     @OA\Response(response=422, description="Erreur de validation"),
 *     @OA\Response(response=500, description="Erreur interne du serveur")
 * )
 */
    
    public function AjoutProduit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_produit' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix' => ['required', 'integer', 'min:0'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            'images' => ['required', 'image', 'max:2048'],
        ]);
    
        // Si la validation échoue, renvoyez une réponse avec les erreurs
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
      
        
        if(Auth::guard('api')->check())
        {
     
        
            $user = Auth::guard('api')->user();
            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->description= $request->description;
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
 *     summary="Mettre à jour un produit",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du produit à mettre à jour",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom_produit", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="quantite", type="integer"),
 *                 @OA\Property(property="prix", type="integer"),
 *                 @OA\Property(property="categorie_id", type="integer"),
 *                 @OA\Property(property="images", type="string", format="binary")
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
 *     @OA\Response(response=401, description="Non autorisé - Jeton invalide"),
 *     @OA\Response(response=403, description="Accès refusé - Utilisateur non autorisé à modifier cette annonce"),
 *     @OA\Response(response=404, description="Produit non trouvé"),
 *     @OA\Response(response=422, description="Erreur de validation"),
 *     @OA\Response(response=500, description="Erreur interne du serveur")
 * )
 */


    public function updateproduit(Request $request,$id)
    {

        $validator = Validator::make($request->all(), [
            'nom_produit' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix' => ['required', 'integer', 'min:0'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
      
    
        $produit = Produit::find($id);
       
         if (!$produit) {
                return response()->json([
                    "status" => false,
                    "message" => "annonce non trouver "
                ]);
            }
    
            // Vérifier si l'utilisateur est le propriétaire de l'annonce
            if ($produit->user_id !==  Auth::guard('api')->user()->id){
                return response()->json([
                    "status" => false,
                    "message" => "annonce non trouver "
                ],403);
            }
           $produit->nom_produit = $request->nom_produit;
            $produit->description= $request->description;
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
        $produit->update();
    
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
     *  security={
     *         {"bearerAuth": {}}
     *     },
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

/**
 * @OA\Post(
 *     path="/api/produitrecherche",
 *     summary="Recherche de produits",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="search", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Produits trouvés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="products", type="array"),
 *             @OA\Property(property="item", type="string"),
 *             @OA\Property(property="categories", type="array"),
 *             @OA\Property(property="newProduct", type="array")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */
 public function Produitrecherche(Request $request){

    $request->validate(['search' => "required"]);

    $item = $request->search;
    // $categories = Category::orderBy('category_name','ASC')->get();
    $products = Produit::where('nom_produit','LIKE',"%$item%")->get();
    $newProduct = Produit::orderBy('id','DESC')->limit(3)->get();
    // return ('frontend.product.search',compact('products','item','categories','newProduct'));
    return response()->json(compact('products','item','categories','newProduct'), 200);

}// End Method


/**
 * @OA\Post(
 *     path="/api/rechercheproduit",
 *     summary="Recherche de produits",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="search", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Produits trouvés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="products", type="array")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */


public function rechercheProduit(Request $request){

    $request->validate(['search' => "required"]);

    $item = $request->search;
    $products = Produit::where('nom_produit','LIKE',"%$item%")->select('nom_produit','product_slug','product_thambnail','selling_price','id')->limit(6)->get();

    // return view('frontend.product.search_product',compact('products'));
    return response()->json(compact('products'), 200);

    

} 



 
}
