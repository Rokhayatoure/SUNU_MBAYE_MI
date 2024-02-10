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
 
    public function listeProduitAgriculteur()
    {
        
        
     $produit = Produit::where('user_id',auth()->guard('api')->user()->id)->get();
        return response()->json(compact('produit'), 200);
    }

     
    public function listeProduit()
    {
        
         $produit=Produit::all();
        // $panier = Produit::where('user_id',auth()->guard('api')->user()->id)->get();
        return response()->json(compact('produit'), 200);
    }

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

    





    public function supProduit($id)
    { 
        
        if(!Auth::guard('api')->check()){
            return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);

        }
        
      $produit=  Produit::find($id);
      $user = Auth::guard('api')->user();
      if ($user && $produit->user_id !== $user->id) {
        return response()->json([
            "status" => false,
            "message" => "Vous n'êtes pas autorisé à supprimer ce produit."
        ]);
        $produit->delete();
    }

        return response()->json(['message' => 'produit supprimé avec succès'], 200);
    }


 public function Produitrecherche(Request $request){

    $request->validate(['search' => "required"]);

    $item = $request->search;
    // $categories = Category::orderBy('category_name','ASC')->get();
    $products = Produit::where('nom_produit','LIKE',"%$item%")->get();
    $newProduct = Produit::orderBy('id','DESC')->limit(3)->get();
    // return ('frontend.product.search',compact('products','item','categories','newProduct'));
    return response()->json(compact('products','item','categories','newProduct'), 200);

}// End Method




public function rechercheProduit(Request $request){

    $request->validate(['search' => "required"]);

    $item = $request->search;
    $products = Produit::where('nom_produit','LIKE',"%$item%")->select('nom_produit','product_slug','product_thambnail','selling_price','id')->limit(6)->get();

    // return view('frontend.product.search_product',compact('products'));
    return response()->json(compact('products'), 200);

    

} 

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
