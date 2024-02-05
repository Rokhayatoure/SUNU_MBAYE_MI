<?php

namespace App\Http\Controllers;



use App\Models\Panier;
use App\Models\Produit;
use App\Models\Commende;
use App\Models\DetailCommende;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CommendeController extends Controller
{
  
      
      /**
     * Passer une commande.
     *
     * @OA\Post(
     *     path="/api/commenender",
     *     summary="Passer une commande",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Commande passée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Commande passée avec succès"),
     *             @OA\Property(property="commende_id", type="integer"),
     *             @OA\Property(property="total_prix", type="float")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Le panier est vide"),
     *     @OA\Response(response=403, description="Non autorisé")
     * )
     */
//     public function Commander()
//       {
  
          
//           $user = Auth::guard('api')->user();
  
//           $panier = Panier::where('user_id',auth()->guard('api')->user()->id)->get();
//       if(!$panier){
//           return response()->json([
//               "status" => false,
//               "message" => "Veillez ajoutez des produits dans le panier ",
             
//           ],500);
  
  
//       }
//           $commende = new Commende();
//           $commende->livraison = 'En_court';
//           $commende->user_id= auth()->guard('api')->user()->id;
//           $commende->nom=$user->nom;
//           $commende->prenom =$user->prenom;
//           $cptQ = 0;
//           $cptC = 0;
//         // Ajoutez chaque article du panier à la table de commande produit
//         foreach( $panier as $item) {
       
//          $cptQ+= $item->quantite;
//          $cptC+=$item->prix;
     
//      }
//      $commende->quantite =$cptQ;
//      $commende->prix =$cptC;
   
   

//   //    dd($cptQ);
//       // Supprimez tous les articles du panier de l'utilisateur après la création de la commande
//       panier::where('user_id', $user->id)->delete();
//       $commende->save();
//       $commende_id = $commende->id;

//       return view('index', compact('cptC','commende_id'));
     
//            }




public function Commender(Request $request)
    {
        if (Auth::guard('api')->check()){
        $commende=new Commende();
        $commende->user_id=auth()->guard('api')->user()->id;
        $commende->save();
        $cptC = 0;
        $cptQ=0;
        $commende_id=$commende->id;
       foreach( $request->input('panier') as $produit) {
            $detailecommende=DetailCommende::create([
            'commende_id'=>$commende->id,
            'produit_id'=>$produit['produit_id'],
            'nombre_produit'=>$produit['nombre_produit'],
            'montant'=>$produit['montant'],
          ]);
     Produit::where('id',$produit['produit_id'])->decrement('quantite',$produit['montant']);
     $cptQ+= $produit['montant'];
     $cptC+=$produit ['nombre_produit']*$produit['montant'];
     }
     $detailecommende->quantite =$cptQ;
      $detailecommende->prix =$cptC;

        $detailecommende->save(); 
    }   else{
        return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
    }
        return response()->json([
            'status'=>true,
            'payment_url'=>"http://127.0.0.1:8000/api/payment?cptC={$cptC}&commende_id={$commende_id}"
        ]);
        //  return view('index', compact('cptC','commende_id'));
        }
  /**
     * Afficher les commandes de l'utilisateur.
     *
     * @OA\Get(
     *     path="/api/afficheCommende",
     *     summary="Afficher les commandes de l'utilisateur",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Liste des commandes de l'utilisateur",
     *         @OA\JsonContent(
     *             @OA\Property(property="commende", type="array", @OA\Items(ref="#/components/schemas/Commende"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé")
     * )
     */
  
           public function AfficheCommende ()
           {
               $id = Auth::guard('api')->user()->id;
               $commende = Commende::where('user_id', $id)->get();
               $dateCommande = $commende->created_at;
               return response()->json(compact('commende','dateCommande'), 200);
           }
   
  /**
     * Afficher les détails d'une commande.
     *
     * @OA\Get(
     *     path="/api/voirPlus/{commende_id}",
     *     summary="Afficher les détails d'une commande",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="commende_id",
     *         in="path",
     *         required=true,
     *         description="ID de la commande",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la commande",
     *         @OA\JsonContent(
     *             @OA\Property(property="commende", type="object", ref="#/components/schemas/Commende")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Veillez vous connecter d'abord"),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
           public function voirPlus( $commende_id)
           {
               if (Auth::guard('api')->check())
               {
                   $commende = Commende::find($commende_id);
                   return response()->json(compact('commende'), 200);
           
       
               }
               else{
                   return response()->json(['message' => ' Veiller vous connecter dabord'], 201);
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

   

