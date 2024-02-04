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
    public function ajouterMessage(Request $request)
    {
        // Validation des données du formulaire
        // $request->validate([
        //     'nom' => 'required|string',
        //     'email' => 'required|email',
        //     'telephone' => 'required|string',
        //     'message' => 'required|string',
        // ]);

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
