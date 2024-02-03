<?php

namespace App\Http\Controllers;

use App\Models\panier;
use App\Models\Produit;
use App\Models\Commende;
use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DetailCommendeController extends Controller
{
    private $paymentController;

    // public function __construct( PayementController $paymentController)
    // {
    //     $this->paymentController = $paymentController;
    // }

    /**
 * @OA\Post(
 *     path="/api/effectuerCommande",
 *     summary="Effectue une commande",
 *     @OA\Response(
 *         response=200,
 *         description="Commande effectuée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Commande effectuée avec succès")
 *         )
 *     ),
 *  security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(response=422, description="Non autorisé"),
 * )
 */

    public function Commander()
    {
        dd('ok');
        // set_time_limit(0);

        // Récupérez le produit par son ID
        // if (!Auth::guard('api')->check()) {
        //     return response()->json(['errors' => 'veilleir vous connecter avant de fair cette action.'], 422);
        // }
        $user = Auth::guard('api')->user();

        $panier = Panier::where('user_id',auth()->guard('api')->user()->id)->get();
    if(!$panier){
        return response()->json([
            "status" => false,
            "message" => "Veillez ajoutez des produits dans le panier ",
           
        ],500);


    }
        $commende = new Commende();
        $commende->livraison = 'En_court';
        $commende->user_id= auth()->guard('api')->user()->id;
        $commende->noam=$user->nom;
        $commende->prenom =$user->prenom;
        $cptQ = 0;
        $cptC = 0;
      // Ajoutez chaque article du panier à la table de commande produit
      foreach( $panier as $item) {
     
       $cptQ+= $item->quantite;
       $cptC+=$item->prix;
   
   }
   $commende->quantite =$cptQ;
   $commende->prix =$cptC;
//    dd($cptQ);
    // Supprimez tous les articles du panier de l'utilisateur après la création de la commande
    panier::where('user_id', $user->id)->delete();
    $commende->save();
    // return view('index', compact('cptc','commende_id'));
    return response()->json([
        'status' => true,
        'commende' => $commende
    ], 201);
         }





        /**
 * @OA\Get(
 *     path="/api/AfficheCommende",
 *     summary="Affiche les commandes de l'utilisateur",
 *  security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Commandes affichées avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="commende", type="array", @OA\Items(ref="#/components/schemas/Commende"))
 *         )
 *     ),
 * )
 */

   public function AfficheCommende ()
    {
        $id = Auth::guard('api')->user()->id;
        $commende = Commende::where('user_id', $id)->get();
        return response()->json(compact('commende'), 200);
    }


/**
 * @OA\Get(
 *     path="/api/voirPlus/{commende_id}",
 *     summary="Affiche plus de détails sur une commande",
 *     @OA\Parameter(
 *         name="commende_id",
 *         in="path",
 *         description="ID de la commande",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Détails de la commande affichés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="commende", type="object", ref="#/components/schemas/Commende")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Non connecté"),
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
 * @OA\Delete(
 *     path="/api/suprimmerCommende/{commende_id}",
 *     summary="Supprime une commande",
 *     @OA\Parameter(
 *         name="commende_id",
 *         in="path",
 *         description="ID de la commande à supprimer",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Commande supprimée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Commande supprimée avec succès")
 *         )
 *     ),
 *     @OA\Response(response=403, description="Non autorisé"),
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
 * @OA\Put(
 *     path="/api/AnnulerLivraison/{commende_id}",
 *     summary="Annule une livraison",
 *     @OA\Parameter(
 *         name="livraison_id",
 *         in="path",
 *         description="ID de la livraison à annuler",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Livraison annulée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Livraison annulée avec succès")
 *         )
 *     ),
 * )
 */
    
    public function AnnulerLivraison($livraison_id)
    {
        $livraison = Commende::findOrFail($livraison_id);
    
    
        $livraison->livraison = 'annuler';
        $livraison->save();
    
        return response()->json(['message' => 'livraison annuler avec succes.'], 200);
    }
/**
 * @OA\Put(
 *     path="/api/LivraisonTerminer/{commende_id}",
 *     summary="Termine une livraison",
 *     @OA\Parameter(
 *         name="livraison_id",
 *         in="path",
 *         description="ID de la livraison à terminer",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 * security={
     *         {"bearerAuth": {}}
     *     },
 *     @OA\Response(
 *         response=200,
 *         description="Livraison terminée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Livraison terminée avec succès")
 *         )
 *     ),
 * )
 */

    public function LivraisonTerminer($livraison_id)
    {
        $livraison = Commende::findOrFail($livraison_id);
    
    
        $livraison->livraison = 'terminer';
        $livraison->save();
    
        return response()->json(['message' => 'livraison terminer avec succes.'], 200);
    }

    
    }


