<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
 *  security={
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
                "message" => "utilisateur connecter inscrit avec succes ",
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
     *     path="/api/modifierAnnonce/{id}",
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
     *  security={
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
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $annonce = Annonce::find($id);
    
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
    if (Auth::guard('api')->check()) {
        
        $annonce = Annonce::find($id);

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


public function listeAnnonceAgriculteur()
{
    
    
    $anonces=Annonce::where('user_id',auth()->guard('api')->user()->id)->get();
    return response()->json(compact('anonces'), 200);
}

    
}
