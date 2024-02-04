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
 /**
     * Liste toutes les catégories.
     *
     * @OA\Get(
     *     path="/api/listeCategorie",
     *     summary="Liste de toutes les catégories",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories",
     *         @OA\JsonContent(
     *             @OA\Property(property="categorie", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */

    public function listeCategorie(Request $request)
    {
       
        $categorie=Categorie::all();
        return response()->json(compact('categorie'), 200);
      
    }

    /**
     * Afficher les détails d'une catégorie.
     *
     * @OA\Get(
     *     path="/api/voiplusCategorie/{id}",
     *     summary="Détails d'une catégorie",
     *     @OA\Parameter(
     *         name="categori_id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la catégorie",
     *         @OA\JsonContent(
     *             @OA\Property(property="categorie", type="object", ref="#/components/schemas/Categorie")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
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

    /**
     * Ajouter une nouvelle catégorie.
     *
     * @OA\Post(
     *     path="/api/AjoutCategorie",
     *     summary="Ajouter une catégorie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom_categories", type="string")
     *         )
     *     ),
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie ajoutée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Catégorie ajoutée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
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

   

 /**
     * Modifier une catégorie existante.
     *
     * @OA\Put(
     *     path="/api/modifieCategorie/{id}",
     *     summary="Modifier une catégorie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom_categories", type="string")
     *         )
     *     ),
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Catégorie modifiée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */

   
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
/**
     * Supprimer une catégorie.
     *
     * @OA\Delete(
     *     path="/api/supCategorie/{id}",
     *     summary="Supprimer une catégorie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Catégorie supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function destroy($id)
    {
      Categorie::find($id)->delete();
    return response()->json(['message' => 'categorie supprimé avec succès'], 200);
    
    }
}
