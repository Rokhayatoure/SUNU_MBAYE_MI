<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Mail\maildeConfirmation;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AnnonceController extends Controller
{


 /**
     * Ajouter une nouvelle annonce.
     *
     * @OA\Post(
     *     path="/api/ajoutAnnonce",
     *     summary="Ajouter une annonce",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"titre", "description", "images"},
     *                 @OA\Property(property="titre", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="images", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="Annonce ajoutée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Annonce ajoutée avec succès"),
     *             @OA\Property(property="annonce", type="object", ref="#/components/schemas/Annonce")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */

   
    public function ajoutAnnonce(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'images' => ['required', 'image', 'max:2048'], // Assurez-vous que la taille maximale est appropriée
        ]);
    
        $validator->messages([
            'titre.required' => 'Le champ titre est obligatoire.',
            'titre.string' => 'Le champ titre doit être une chaîne de caractères.',
            'titre.max' => 'Le champ titre ne peut pas dépasser :max caractères.',
            
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'Le champ description doit être une chaîne de caractères.',
            
            'images.required' => 'Le champ images est obligatoire.',
            'images.image' => 'Le champ images doit être une image.',
            // 'images.max' => 'La taille de l\'image ne peut pas dépasser :max kilo-octets.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
       if (Auth::guard('api')->check()) {
       
            $user = Auth::guard('api')->user();
            $annonce = new Annonce();
            $annonce->titre = $request->titre;
            $annonce->description = $request->description;
            $annonce->user_id = $user->id;
      
            
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $annonce->images = $filename;
            }
    
            
            $annonce->save();
    
            return response()->json([
                "status" => true,
                "message" => "anonce ajoter avec succes ",
                'annonce'=>$annonce
            ]);
        }
        else{
            return response()->json([
                "status" => false,
                "message" => "Veiller vous connecter dabord "
            ]);
        }
    }


 /**
     * Modifier une annonce existante.
     *
     * @OA\Put(
     *     path="/api/modifierAnnonce{id}",
     *     summary="Modifier une annonce",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="titre", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="images", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Annonce modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Annonce modifiée avec succès"),
     *             @OA\Property(property="annonce", type="object", ref="#/components/schemas/Annonce")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Annonce non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */

    public function modifierAnnonce(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            // 'images' => ['required', 'image', 'max:2048'], // Assurez-vous que la taille maximale est appropriée
        ]);
    
        $validator->messages([
            'titre.required' => 'Le champ titre est obligatoire.',
            'titre.string' => 'Le champ titre doit être une chaîne de caractères.',
            'titre.max' => 'Le champ titre ne peut pas dépasser :max caractères.',
            
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'Le champ description doit être une chaîne de caractères.',
            
            'images.required' => 'Le champ images est obligatoire.',
            'images.image' => 'Le champ images doit être une image.',
            'images.max' => 'La taille de l\'image ne peut pas dépasser :max kilo-octets.',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = Auth::guard('api')->user();
        if (Auth::guard('api')->check()) {
           
            $annonce = Annonce::find($id);
            if ($user->id !== $annonce->user_id) {
                return response()->json([
                    "status" => false,
                    "message" => "Vous n'êtes pas autorisé à modifier cette annonce."
                ], 403);
            }
            if (!$annonce) {
                return response()->json([
                    "status" => false,
                    "message" => "annonce non trouver "
                ]);
            }
    
           
            $annonce->titre = $request->titre ?? $annonce->titre;
            $annonce->description = $request->description ?? $annonce->description;
    
            // Gérer l'upload de la nouvelle image si fournie
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $annonce->images = $filename;
            }
    
            // Enregistrer les modifications
            $annonce->save();
    
            return response()->json([
                "status" => true,
                "message" => "annonce modifier  avec succes ",
                'annonce'=>$annonce
            ]);
        } else {
            return response()->json(['message' => 'Veillez vous connecter d\'abord'], 401);
        }
    }
    

    /**
     * Liste de toutes les annonces.
     *
     * @OA\Get(
     *     path="/api/listAnnonce",
     *     summary="Liste de toutes les annonces",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des annonces",
     *         @OA\JsonContent(
     *             @OA\Property(property="anonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function listAnnonce(Request $request) 

    {
        $anonces=Annonce::all();
        return response()->json(compact('anonces'), 200);
    } 


/**
     * Afficher les détails d'une annonce.
     *
     * @OA\Get(
     *     path="/api/voirPlus/{annonce_id}",
     *     summary="Afficher les détails d'une annonce",
     *     @OA\Parameter(
     *         name="annonce_id",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'annonce",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonce", type="object", ref="#/components/schemas/Annonce")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
    */
 public function voirPlus( $annonce_id)
    {
        if (Auth::guard('api')->check())
        {
            $annonce = Annonce::find($annonce_id);
            return response()->json(compact('annonce'), 200);
    

        }
        else{
            return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
        }
    }
/**
 * Supprimer une annonce.
 *
 * @OA\Delete(
 *     path="/api/supAnnonce/{id}",
 *     summary="Supprimer une annonce",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'annonce à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *  security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Annonce supprimée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Annonce supprimée avec succès")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Annonce non trouvée"),
 *     @OA\Response(response=403, description="Vous n'êtes pas autorisé à supprimer cette annonce")
 * )
 */
public function supprimerAnnonce($id)
{

    $user = Auth::guard('api')->user();
    if (Auth::guard('api')->check()) {
        
        $annonce = Annonce::find($id);
        if ($user->id !== $annonce->user_id) {
            return response()->json([
                "status" => false,
                "message" => "Vous n'êtes pas autorisé à suprimer cette annonce."
            ], 403);
        }
        if (!$annonce) {
            return response()->json(['message' => 'Annonce non trouvée'], 404);
        }

        $annonce->delete();

        return response()->json([
            "status" => true,
            "message" => "annonce suprimer  avec succes ",
            'annonce'=>$annonce
        ]);
    } else {
        return response()->json(['message' => 'Veillez vous connecter d\'abord'], 401);
    }
}

/**
     * Liste des annonces en fonction du users.
     *
     * @OA\Get(
     *     path="/api/listeAnnonceAgriculteur",
     *     summary="Liste de toutes les annonces",
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Liste des annonces",
     *         @OA\JsonContent(
     *             @OA\Property(property="anonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
public function listeAnnonceAgriculteur()
{
    
    
    $anonces=Annonce::where('user_id',auth()->guard('api')->user()->id)->get();
    return response()->json(compact('anonces'), 200);
}



/**
 * Publier une annonce sur la page d'accueil.
 *
 * @OA\Post(
 *     path="/api/publierAnnonce/{id}",
 *     summary="Publier une annonce sur la page d'accueil",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'annonce à publier",
 *         @OA\Schema(type="integer")
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Annonce publiée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Annonce publiée avec succès"),
 *             @OA\Property(property="annonce", type="object", ref="#/components/schemas/Annonce")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=404, description="Annonce non trouvée"),
 *     @OA\Response(response=403, description="Vous avez atteint la limite hebdomadaire de publication d'annonces")
 * )
 */
public function publierAnnonce(Request $request, $id)
{
    if (Auth::guard('api')->check()) {
        $user = Auth::guard('api')->user();

        // Vérifier si l'utilisateur a déjà publié trois annonces cette semaine
        $annoncesPublieesCetteSemaine = Annonce::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        if ($annoncesPublieesCetteSemaine >= 3) {
            return response()->json([
                "status" => false,
                "message" => "Vous avez déjà publié trois annonces cette semaine. Supprimez une annonce pour en publier une nouvelle.",
            ]);
        }
// Vérifier si l'annonce existe
    $annonce = Annonce::find($id);
 if (!$annonce) {
            return response()->json([
                "status" => false,
                "message" => "Annonce non trouvée.",
            ]);
        }

        // Marquer l'annonce comme publiée
        $annonce->is_published = true;
        $annonce->save();
        // if ($annonce->is_published === true) {
        //     $succes = [
        //         'title' => 'Mail de validation confirmation',
        //         'body' => 'Votre payment a été bien pris en compte et votre annonce a été bien publiée.',
        //     ];
    
        //     Mail::to($user->email)->send(new maildeConfirmation($succes));
    
        // }
      
        
        return response()->json([
            "status" => true,
            "message" => "Annonce publiée avec succès.",
            'annonce' => $annonce
        ]);
    } else {
        return response()->json(['message' => 'Veuillez vous connecter d\'abord'], 401);
    }
}
/**
 * Retirer une annonce de la page d'accueil.
 *
 * @OA\Delete(
 *     path="/api/retirerAnnonce/{id}",
 *     summary="Retirer une annonce de la page d'accueil",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'annonce à retirer",
 *         @OA\Schema(type="integer")
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Annonce retirée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Annonce retirée avec succès"),
 *             @OA\Property(property="annonce", type="object", ref="#/components/schemas/Annonce")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=404, description="Annonce non trouvée")
 * )
 */
public function retirerAnnonce($id)
{
    $user = Auth::guard('api')->user();
    if (Auth::guard('api')->check()) {
        $annonce = Annonce::find($id);
        
        
        if (!$annonce) {
            return response()->json(['message' => 'Annonce non trouvée'], 404);
        }

        // Retirer de la page d'accueil
        $annonce->is_published = false;
        $annonce->save();

        return response()->json([
            "status" => true,
            "message" => "Annonce retirée avec succès.",
            'annonce' => $annonce
        ]);
    } else {
        return response()->json(['message' => 'Veuillez vous connecter d\'abord'], 401);
    }
}



/**
 * Liste des annonces publiées.
 *
 * @OA\Get(
 *     path="/api/listeAnnoncesPubliees",
 *     summary="Liste de toutes les annonces publiées",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Liste des annonces publiées",
 *         @OA\JsonContent(
 *             @OA\Property(property="annonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé")
 * )
 */
public function listeAnnoncesPubliees()
{
    // Récupérer toutes les annonces publiées
    $annoncesPubliees = Annonce::where('is_published', true)->get();

    return response()->json(compact('annoncesPubliees'), 200);
}
}
