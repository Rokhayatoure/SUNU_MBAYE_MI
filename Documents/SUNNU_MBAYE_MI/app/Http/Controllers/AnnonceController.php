<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
   
    public function ajoutAnnonce(Request $request)
    {
       
        if (Auth::guard('api')->check()) {
       
            $user = Auth::guard('api')->user();
            $annonce = new Annonce();
            $annonce->titre = $request->titre;
            $annonce->description = $request->description;
            $annonce->users_id = $user->id;
      
            
            if ($request->hasFile('images')) {
                $file = $request->file('images');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $annonce->images = $filename;
            }
    
            
            $annonce->save();
    
            return response()->json(['message' => 'Annonce ajoutée avec succès'], 201);
        }
        else{
            return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
        }
    }


    public function modifierAnnonce(Request $request, $id)
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $annonce = Annonce::find($id);
    
            if (!$annonce) {
                return response()->json(['message' => 'Annonce non trouvée'], 404);
            }
    
            // Vérifier si l'utilisateur est le propriétaire de l'annonce
            if ($annonce->users_id !== $user->id) {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette annonce'], 403);
            }
    
            // Mettre à jour les champs de l'annonce
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
    
            return response()->json(['message' => 'Annonce modifiée avec succès'], 200);
        } else {
            return response()->json(['message' => 'Veillez vous connecter d\'abord'], 401);
        }
    }
    

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

    
}
