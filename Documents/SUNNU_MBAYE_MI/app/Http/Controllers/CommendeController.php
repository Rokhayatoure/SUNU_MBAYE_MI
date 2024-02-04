<?php

namespace App\Http\Controllers;



use App\Models\Panier;
use App\Models\Commende;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CommendeController extends Controller
{
  
      public function Commander()
      {
  
          
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
          $commende->nom=$user->nom;
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
      $commende_id = $commende->id;

      return view('index', compact('cptC','commende_id'));
     
           }
  
  
           public function AfficheCommende ()
           {
               $id = Auth::guard('api')->user()->id;
               $commende = Commende::where('user_id', $id)->get();
               return response()->json(compact('commende'), 200);
           }
   

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
           public function suprimmerCommende($commende_id)
           {
               if(!Auth::guard('api')->check()){
                   return response()->json(['message' => 'veiller vouss connecter avant de faire cette action'], 403);
       
               }
               Commende::find($commende_id)->delete();
               return response()->json(['message' => 'commende supprimé avec succès'], 200);
           }

           public function AnnulerLivraison($livraison_id)
           {
               $livraison = Commende::findOrFail($livraison_id);
           
           
               $livraison->livraison = 'annuler';
               $livraison->save();
           
               return response()->json(['message' => 'livraison annuler avec succes.'], 200);
           }
           public function LivraisonTerminer($livraison_id)
           {
               $livraison = Commende::findOrFail($livraison_id);
           
           
               $livraison->livraison = 'terminer';
               $livraison->save();
           
               return response()->json(['message' => 'livraison terminer avec succes.'], 200);
           }
       
   }

   

