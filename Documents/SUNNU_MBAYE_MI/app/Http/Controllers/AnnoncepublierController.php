<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Models\annoncepublier;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AnnoncepublierController extends Controller
{
    public function publierAnnonce(Request $request, $id)
{
    if (Auth::guard('api')->check()) {
        $user = Auth::guard('api')->user();

        // Vérifier si l'utilisateur a déjà publié trois annonces cette semaine
        $annoncesPublieesCetteSemaine = AnnoncePublier::where('user_id', $user->id)
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

        // Vérifier si l'annonce est déjà publiée
        $annoncePubliee = AnnoncePublier::where('user_id', $user->id)
            ->where('annonce_id', $id)
            ->first();

        if ($annoncePubliee) {
            return response()->json([
                "status" => false,
                "message" => "Cette annonce est déjà publiée.",
            ]);
        }

        // Publier l'annonce
        $annoncePubliee = new AnnoncePublier();
        $annoncePubliee->user_id = $user->id;
        $annoncePubliee->annonce_id = $id;
        $annoncePubliee->save();

        return response()->json([
            "status" => true,
            "message" => "Annonce publiée avec succès.",
            'annonce_publiee' => $annoncePubliee
        ]);
    } else {
        return response()->json(['message' => 'Veillez vous connecter d\'abord'], 401);
    }
}

public function retirerAnnonce(Request $request, $id)
{
   
    $user = Auth::guard('api')->user();

        // Vérifier si l'annonce est publiée par l'utilisateur
        $annoncePubliee = AnnoncePublier::where('user_id', $user->id)
            ->where('annonce_id', $id)
            ->first();

        if (!$annoncePubliee) {
            return response()->json([
                "status" => false,
                "message" => "Vous n'êtes pas autorisé à retirer cette annonce.",
            ]);
        }

        // Retirer l'annonce
        $annoncePubliee->delete();

        return response()->json([
            "status" => true,
            "message" => "Annonce retirée avec succès.",
        ]);
    
}


}
