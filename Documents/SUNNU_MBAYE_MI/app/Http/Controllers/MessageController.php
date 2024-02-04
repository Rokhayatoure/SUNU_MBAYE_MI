<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
}
