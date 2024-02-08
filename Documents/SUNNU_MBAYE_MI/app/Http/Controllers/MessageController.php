<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Mail\ResponseMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
 * Ajouter un nouveau message.
 *
 * @OA\Post(
 *     path="/api/ajouterMessage",
 *     summary="Ajouter un message",
 *     tags={"Messages"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"nom", "email", "telephone", "message"},
 *                 @OA\Property(property="nom", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="telephone", type="string"),
 *                 @OA\Property(property="message", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Message ajouté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Message ajouté avec succès"),
 *             @OA\Property(property="message_data", type="object", ref="#/components/schemas/Message")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */
    public function ajouterMessage(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'nom' => 'required|string',
            'email' => 'required|email',
            'telephone' => 'required|string',
            'message' => 'required|string',
        ]);

        // Création d'un nouveau message
        $message = new Message([
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'message' => $request->message,
        ]);

        // Enregistrement du message dans la base de données
        $message->save();

        return response()->json([
            'status' => true,
            'message' => 'Message ajouté avec succès',
            'message_data' => $message,
        ]);
    }

/**
 * Lister tous les messages.
 *
 * @OA\Get(
 *     path="/api/listerMessages",
 *     summary="Lister tous les messages",
 *     tags={"Messages"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Liste de tous les messages",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="messages", type="array", @OA\Items(ref="#/components/schemas/Message"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé")
 * )
 */
    public function listerMessages()
    {
        // Récupérer tous les messages
        $messages = Message::all();

        return response()->json([
            'status' => true,
            'messages' => $messages,
        ]);
    }


    /**
 * Afficher les détails d'un message.
 *
 * @OA\Get(
 *     path="/api/voirplusmessage/{id}",
 *     summary="Afficher les détails d'un message",
 *     tags={"Messages"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du message à afficher",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails du message",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", ref="#/components/schemas/Message")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Message non trouvé"
 *     )
 * )
 */
public function voirplusmessage($id){
     // Récupérer le message par son ID
     $message = Message::findOrFail($id);
     return response()->json(compact('message'));
}


   

/**
 * Envoyer une réponse à un message.
 *
 * @OA\Post(
 *     path="/api/reponse",
 *     summary="Envoyer une réponse à un message",
 *     tags={"Messages"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données de la réponse",
 *         @OA\JsonContent(
 *             required={"email", "message"},
 *             @OA\Property(property="email", type="string", format="email", description="Adresse e-mail du destinataire"),
 *             @OA\Property(property="message", type="string", description="Contenu de la réponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Réponse envoyée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", description="Message de confirmation")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */


public function reponse(Request $request)
    {
        try {
            $data = $request->message;
            if (Mail::to($request->email)->send(new ResponseMail($data))) {
                return response()->json(['message' => 'reponse envoye avec success']);
            } else {
                return response()->json(['message' => 'reponse non envoyer']);
            }
        } catch (\Throwable $th) {
            return  $th->getMessage();
        }
    }




}





