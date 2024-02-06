<?php

namespace App\Http\Controllers;



use App\Models\Panier;
use App\Models\Produit;
use App\Models\Commende;
use App\Models\DetailCommende;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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


  public function ListerCommende(Request $request)
{
    if (Auth::guard('api')->check()){
        
        $commandes = Commende::all();
        $payement = Payment::all();
        return response()->json(['status' => true, 'commandes' => $commandes,'Payement'=>$payement]);
    } else {
        return response()->json(['status' => false, 'message' => 'Veuillez vous connecter d\'abord'], 201);
    }
}

// Cette fonction renvoie les détails d'une commande spécifiée par son id
public function VoirplusCommende(Request $request, $id)
{
    if (Auth::guard('api')->check()){
        
        $commande = Commende::find($id);
        if ($commande){
            $details = DetailCommende::find('commende_id', $id);
            $payement = Payment::find('commende_id', $id);
            return response()->json(['status' => true, 'commande' => $commande, 'details' => $details,'Payement'=>$payement]);
        } else {
            return response()->json(['status' => false, 'message' => 'Commande introuvable'], 404);
        }
    } else {
        return response()->json(['status' => false, 'message' => 'Veuillez vous connecter d\'abord'], 201);
    }
}


           
    /**
     * Supprimer une commande.
     *
     * @OA\Delete(
     *     path="/api/supprimerCommende/{commende_id}",
     *     summary="Supprimer une commande",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="commende_id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Commande supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Commande supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Veillez vous connecter d'abord"),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
           public function suprimmerCommende($commende_id)
           {
               if(!Auth::guard('api')->check()){
                   return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);
       
               }
               Commende::find($commende_id)->delete();
               return response()->json(['message' => 'commende supprimé avec succès'], 200);
           }
/**
     * Annuler la livraison d'une commande.
     *
     * @OA\Put(
     *     path="/api/annulerLivraison/{$commende_id}",
     *     summary="Annuler la livraison d'une commande",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="commende_id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande à annuler la livraison",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livraison annulée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Livraison annulée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
           public function AnnulerLivraison($commende_id)
           {
               $livraison = Commende::findOrFail($commende_id);
           
           
               $livraison->livraison = 'annuler';
               $livraison->save();
           
               return response()->json(['message' => 'livraison annuler avec succes.'], 200);
           }


           /**
     * Marquer la livraison d'une commande comme terminée.
     *
     * @OA\Put(
     *     path="/api/livraisonTerminer/{commende_id}",
     *     summary="Marquer la livraison d'une commande comme terminée",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="commende_id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande à marquer comme terminée",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livraison terminée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Livraison terminée avec succès")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
           public function LivraisonTerminer($commende_id)
           {
               $livraison = Commende::findOrFail($commende_id);
           
           
               $livraison->livraison = 'terminer';
               $livraison->save();
           
               return response()->json(['message' => 'livraison terminer avec succes.'], 200);
           }
       
   }

   

