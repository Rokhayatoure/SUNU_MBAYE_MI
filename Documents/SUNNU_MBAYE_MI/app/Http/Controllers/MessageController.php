<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Mail\ResponseMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
  
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
        ],200);
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



public function voirplusmessage($id){
     // Récupérer le message par son ID
     $message = Message::findOrFail($id);
     return response()->json(compact('message'),200);
}


   



public function reponse(Request $request)
    {
        try {
            $data = $request->continue;
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





