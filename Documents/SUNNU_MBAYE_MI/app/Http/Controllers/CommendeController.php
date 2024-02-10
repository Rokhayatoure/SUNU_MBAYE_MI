<?php

namespace App\Http\Controllers;



use App\Models\User;
use App\Models\Panier;
use App\Models\Payment;
use App\Models\Produit;
use App\Models\Commende;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CommendeController extends Controller
{
  
     


public function Commender(Request $request)
    {
        if (Auth::guard('api')->check()){
        $commende=new Commende();
        $commende->user_id=auth()->guard('api')->user()->id;
        $commende->nom=auth()->guard('api')->user()->nom;
        $commende->prenom=auth()->guard('api')->user()->prenom;
        $commende->contact=auth()->guard('api')->user()->telephone;
        $commende->save();
        $cptC = 0;
        
        $commende_id=$commende->id;
        // dd($commende_id);
       foreach( $request->input('panier') as $produit) {
        
            DetailCommende::create([
            'commende_id'=>$commende->id,
            'produit_id'=>$produit['produit_id'],
            'nombre_produit'=>$produit['nombre_produit'],
            'montant'=>$produit['montant'],
            
          ]);
     Produit::where('id',$produit['produit_id'])->decrement('quantite',$produit['montant']);
     $cptC+= $produit['montant'];
    
     }
    

        
    }   else{
        return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
    }
        return response()->json([
            'status'=>true,
            'payment_url'=>"http://127.0.0.1:8000/api/payment?cptC={$cptC}&commende_id={$commende_id}"
        ]);
        //  return view('index', compact('cptC','commende_id'));
        }
  // Cette fonction renvoie la liste des commandes d'un utilisateur authentifié










// Cette fonction renvoie les détails d'une commande spécifiée par son id



           public function suprimmerCommende($commende_id)
           {
               if(!Auth::guard('api')->check()){
                   return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);
       
               }
               Commende::find($commende_id)->delete();
               return response()->json(['message' => 'commende supprimé avec succès'], 200);
           }

           public function AnnulerLivraison($commende_id)
           {
               $livraison = Commende::findOrFail($commende_id);
           
           
               $livraison->livraison = 'annuler';
               $livraison->save();
           
               return response()->json(['message' => 'livraison annuler avec succes.'], 200);
           }


           public function LivraisonTerminer($commende_id)
           {
               $livraison = Commende::findOrFail($commende_id);
           
           
               $livraison->livraison = 'terminer';
               $livraison->save();
           
               return response()->json(['message' => 'livraison terminer avec succes.'], 200);
           }
       




           public function listeCommandes()
           {
               $commandes = Commende::with(['user', 'payment'])
                   ->get(['id', 'user_id', 'livraison']); // Sélectionne seulement les colonnes nécessaires
           
               $commandesList = [];
           
               foreach ($commandes as $commande) {
                   // Récupérer les détails de l'utilisateur
                   $user = User::find($commande->user_id);
           
                   // Récupérer le montant total de la commande à partir de la relation payment
                   $montantTotal = $commande->payment ? $commande->payment->amount : 0;
                   
                   $commandesList[] = [
                       'id' => $commande->id,
                       'nom_utilisateur' => $user->nom,
                       'prenom_utilisateur' => $user->prenom,
                      
                       'etat_livraison' => $commande->livraison,
                       'montant_total' => $montantTotal,
                   ];
               }
           
               return response()->json(['commandes' => $commandesList]);
           }
      
           public function VoirplusCommende($commendeId)
           {
        $details = DetailCommende::with(['produit', 'commende.user'])
    ->where('commende_id', $commendeId)
    ->get();

$detailsList = [];
foreach ($details as $detail) {
    $montantTotal = $detail->montant * $detail->nombre_produit;
   
    
    $detailsList[] = [
        'produit_photo' => $detail->produit->images,
        'produit_nom' => $detail->produit->nom_produit,
        'prix_unitaire' => $detail->montant,
        'quantite' => $detail->nombre_produit,
        'prix_total' => $montantTotal,
    ];
}

return response()->json(['details_commande' => $detailsList]);

      }






      public function VoirplusCommendeRevendeur($commendeId)
      {
       // Récupérer l'utilisateur revendeur authentifié
    $revendeur = auth()->guard('api')->user();
    
    // Vérifier si l'utilisateur est authentifié
    if (!$revendeur) {
        return response()->json(['message' => 'Veuillez vous connecter d\'abord'], 401);
    }

    // Récupérer les détails de la commande associée à l'ID de la commande et au revendeur
    $details = DetailCommende::with(['produit'])
        ->where('commende_id', $commendeId)
        ->whereHas('produit', function ($query) use ($revendeur) {
            $query->where('user_id', $revendeur->id);
        })
        ->get();

    // Tableau pour stocker les détails des commandes
    $detailsList = [];

    // Parcourir les détails de la commande récupérés
    foreach ($details as $detail) {
        $montantTotal = $detail->montant * $detail->nombre_produit;
      
      $detailsList[] = [
      'produit_photo' => $detail->produit->images,
      'produit_nom' => $detail->produit->nom_produit,
      'prix_unitaire' => $detail->montant,
      'quantite' => $detail->nombre_produit,
      'prix_total' => $montantTotal,
      ];
      }
      
      return response()->json(['details_commande' => $detailsList]);
            
            
   }

   
}
