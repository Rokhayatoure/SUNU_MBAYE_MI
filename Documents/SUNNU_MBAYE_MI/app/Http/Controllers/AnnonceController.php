<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Mail\maildeConfirmation;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AnnonceController extends Controller
{



    public function ajoutAnnonce(Request $request)
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
            
            // 'images.required' => 'Le champ images est obligatoire.',
            // 'images.image' => 'Le champ images doit être une image.',
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
            ],200);
        }
        else{
            return response()->json([
                "status" => false,
                "message" => "Veiller vous connecter dabord "
            ]);
        }
    }


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
            
           
            
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = Auth::guard('api')->user();
        if (Auth::guard('api')->check()) {
           
            $annonce = Annonce::find($id);
            // if ($user->id !== $annonce->user_id) {
            //     return response()->json([
            //         "status" => false,
            //         "message" => "Vous n'êtes pas autorisé à modifier cette annonce."
            //     ], 403);
            // }
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
            $annonce->update();
    
            return response()->json([
                "status" => true,
                "message" => "annonce modifier  avec succes ",
                'annonce'=>$annonce
            ]);
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


public function listeAnnonceAgriculteur()
{
    
    
    $anonces=Annonce::where('user_id',auth()->guard('api')->user()->id)->get();
    return response()->json(compact('anonces'), 200);
}




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
    $idUser = $annonce->user_id;
    $utilisateur= User::find($idUser);
    // dd($annonce);
 if (!$annonce) {
            return response()->json([
                "status" => false,
                "message" => "Annonce non trouvée.",
            ]);
        }

        // Marquer l'annonce comme publiée
        $annonce->is_published= true;
        $annonce->save();
        if($annonce->save()){
            Mail::to($utilisateur->email)->send(new maildeConfirmation($annonce));  

        }
        
        return response()->json([
            "status" => true,
            "message" => "Annonce publiée avec succès.",
            'annonce' => $annonce
        ],200);
    } else {
        return response()->json(['message' => 'Veuillez vous connecter d\'abord'], 401);
    }
}

public function retirerAnnonce($id)
{
    $user = Auth::guard('api')->user();
    if (Auth::guard('api')->check()) {
        $annonce = Annonce::find($id);
        
        
        if (!$annonce) {
            return response()->json(['message' => 'Annonce non trouvée'], 404);
        }

        // Retirer de la page d'accueil
        $annonce->is_published= false;
        $annonce->save();

        return response()->json([
            "status" => true,
            "message" => "Annonce retirée avec succès.",
            'annonce' => $annonce
        ],200);
    } else {
        return response()->json(['message' => 'Veuillez vous connecter d\'abord'], 401);
    }
}



public function listeAnnoncesPubliees()
{
    // Récupérer toutes les annonces publiées
    $annoncesPubliees = Annonce::where('is_published', true)->get();

    return response()->json(compact('annoncesPubliees'), 200);
}
}
