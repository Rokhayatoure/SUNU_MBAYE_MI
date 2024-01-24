<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
    // if (!Auth::guard('api')->check()) {
    //     return response()->json(['errors' => 'Veuillez vous connecter avant de faire cette action.'], 422);
    // }

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
    // {
    //     if (!Auth::guard('api')->check()) {
    //         return response()->json(['errors' => 'veilleir vous connecter avant de fair cette action.'], 422);
    //     }
   {
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


        if (!Auth::check()) {
            return response()->json(['errors' => 'veilleir vous connecter avant de fair cette action.'], 422);
        }
        Categorie::find($id)->delete();
    return response()->json(['message' => 'categorie supprimé avec succès'], 200);
    
    }
}
