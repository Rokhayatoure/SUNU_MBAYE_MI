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
            'quantite' => ['required', 'integer', 'min:1'],
            'prix' => ['required', 'integer', 'min:0'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
             'images' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);
        
        $validator->messages([
            'nom_produit.required' => 'Le champ nom du produit est obligatoire.',
            'nom_produit.string' => 'Le champ nom du produit doit être une chaîne de caractères.',
            'nom_produit.max' => 'Le champ nom du produit ne peut pas dépasser :max caractères.',
            
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'Le champ description doit être une chaîne de caractères.',
            
            'quantite.required' => 'Le champ quantité est obligatoire.',
            'quantite.integer' => 'Le champ quantité doit être un entier.',
            'quantite.min' => 'Le champ quantité doit être d\'au moins :min.',
            
            'prix.required' => 'Le champ prix est obligatoire.',
            'prix.integer' => 'Le champ prix doit être un entier.',
            'prix.min' => 'Le champ prix doit être d\'au moins :min.',
            
            'categorie_id.required' => 'Le champ catégorie est obligatoire.',
            'categorie_id.integer' => 'Le champ catégorie doit être un entier.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
        ]);
        
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
        ], 200);
    
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
            'quantite' => ['required', 'integer', 'min:1'],
            'prix' => ['required', 'integer', 'min:0'],
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
             'images' =>['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);
        
        $validator->messages([
            'nom_produit.required' => 'Le champ nom du produit est obligatoire.',
            'nom_produit.string' => 'Le champ nom du produit doit être une chaîne de caractères.',
            'nom_produit.max' => 'Le champ nom du produit ne peut pas dépasser :max caractères.',
            
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'Le champ description doit être une chaîne de caractères.',
            
            'quantite.required' => 'Le champ quantité est obligatoire.',
            'quantite.integer' => 'Le champ quantité doit être un entier.',
            'quantite.min' => 'Le champ quantité doit être d\'au moins :min.',
            
            'prix.required' => 'Le champ prix est obligatoire.',
            'prix.integer' => 'Le champ prix doit être un entier.',
            'prix.min' => 'Le champ prix doit être d\'au moins :min.',
            
            'categorie_id.required' => 'Le champ catégorie est obligatoire.',
            'categorie_id.integer' => 'Le champ catégorie doit être un entier.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
      
      
    
        $produit = Produit::find($id);
        $user = Auth::guard('api')->user();
        if ($user->id !==  $produit->user_id) {
            return response()->json([
                "status" => false,
                "message" => "Vous n'êtes pas autorisé à modifier cette produit."
            ], 403);
        }
       
         if (!$produit) {
                return response()->json([
                    "status" => false,
                    "message" => "produit non trouver "
                ]);
            }
    
            // Vérifier si l'utilisateur est le propriétaire de l'produit
            if ($produit->user_id !==  Auth::guard('api')->user()->id){
                return response()->json([
                    "status" => false,
                    "message" => "vous etes pas autorise a modifier cette produit "
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
        
      $produit=  Produit::find($id)->delete();
      $user = Auth::guard('api')->user();
        if ($user->id !==  $produit->user_id) {
            return response()->json([
                "status" => false,
                "message" => "Vous n'êtes pas autorisé à suprimer cette produit."
            ], 403);
        }
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
/**
 * Filtrer les produits par catégorie.
 *
 * @OA\Post(
 *     path="/api/filtrer-produits-par-categorie",
 *     summary="Filtrer les produits par catégorie",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"categorie_id"},
 *             @OA\Property(property="categorie_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Renvoie les produits filtrés par catégorie",
 *         @OA\JsonContent(
 *             @OA\Property(property="produits", type="array", @OA\Items(ref="#/components/schemas/Produit"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */
public function filtrerProduitsParCategorie(Request $request)
 {
    $request->validate([
        'categorie_id' => 'required|exists:categories,id',
    ]);

    $categorieId = $request->categorie_id;

    // Filtrer les produits par catégorie
    $produits = Produit::where('categorie_id', $categorieId)->get();

    return response()->json(compact('produits'), 200);
}


public function supProduitAdmine($id)
{ 
    
    if(!Auth::guard('api')->check()){
        return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);

    }
    
  $produit=  Produit::find($id)->delete();

    
    return response()->json(['message' => 'produit supprimé avec succès','produit'=>$produit], 200);
}

 
}
