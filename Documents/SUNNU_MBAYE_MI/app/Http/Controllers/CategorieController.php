<?php

namespace App\Http\Controllers;


use App\Models\Role;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{

    public function listeCategorie(Request $request)
    {
       
        $categorie=Categorie::all();
        return response()->json(compact('categorie'), 200);
      
    }

  public function voiplusCategorie($categori_id){

    if (Auth::guard('api')->check())
        {
            $categorie = Categorie::find($categori_id);
            return response()->json(compact('categorie'), 200);
    

        }
        else{
            return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
        }

}

public function AjoutCategorie(Request $request)
{
   
    // Vérifier si l'utilisateur est authentifié
    if (!Auth::guard('api')->check()) {
        return response()->json(['errors' => 'Veuillez vous connecter avant de faire cette action.'], 422);
    }
    $user = Auth::guard('api')->user();

// Définissez le rôle que vous voulez vérifier
$requiredRole = 'admin';

// Vérifiez si l'utilisateur a le bon rôle
if ($user->role->nom_role !== $requiredRole) {
    return response()->json(['errors' => 'Vous n\'avez pas les autorisations nécessaires pour faire cette action.'], 403);
}$validator = Validator::make($request->all(), [
    'nom_categories' => ['required', 'string', 'max:255'],
]);

$validator->messages([
    'nom_categories.required' => 'Le champ nom de la catégorie est obligatoire.',
    'nom_categories.string' => 'Le champ nom de la catégorie doit être une chaîne de caractères.',
    'nom_categories.max' => 'Le champ nom de la catégorie ne peut pas dépasser :max caractères.',
]);


    // Créer une nouvelle instance de la catégorie
    $categorie = new Categorie([
        'nom_categories' => $request->nom_categories,
    ]);

    // Enregistrer la catégorie dans la base de données
    $categorie->save();

    // Retourner une réponse JSON
    return response()->json(['message' => 'Catégorie ajoutée avec succès.'], 200);
}

   


   
    public function modifieCategorie(Request $request, $id)


    
    {
        $validator = Validator::make($request->all(), [
            'nom_categories' => ['required', 'string', 'max:255'],
        ]);
    
        $validator->messages([
            'nom_categories.required' => 'Le champ nom de la catégorie est obligatoire.',
            'nom_categories.string' => 'Le champ nom de la catégorie doit être une chaîne de caractères.',
            'nom_categories.max' => 'Le champ nom de la catégorie ne peut pas dépasser :max caractères.',
        ]);
    
        if (!Auth::guard('api')->check()) {
            return response()->json(['errors' => 'veilleir vous connecter avant de fair cette action.'], 422);
        }
// Obtenez l'utilisateur connecté
$user = Auth::guard('api')->user();

// Définissez le rôle que vous voulez vérifier
$requiredRole = 'admin';

// Vérifiez si l'utilisateur a le bon rôle
if ($user->role->nom_role !== $requiredRole) {
    return response()->json(['errors' => 'Vous n\'avez pas les autorisations nécessaires pour faire cette action.'], 403);
}

   
    $categorie = Categorie::find($id);

    // Vérifiez si la catégorie existe
    if (!$categorie) {
        return response()->json(['message' => 'Catégorie non trouvée'], 404);
    }

    // Mettez à jour les propriétés de la catégorie avec les données de la requête
    $categorie->nom_categories = $request->nom_categories;

    // Sauvegardez la catégorie mise à jour
    $categorie->save();

    // Réponse JSON
    return response()->json(['message' => 'Catégorie modifiée avec succès'], 200);

}

    public function destroy($id)
    {
      Categorie::find($id)->delete();
    return response()->json(['message' => 'categorie supprimé avec succès'], 200);
    
    }
}
