<?php

namespace App\Http\Controllers;


use Exception;
use App\Models\Pagner;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

Session ::Start();
class PagnerController extends Controller
{
    public function ajouterProduitPanier(Produit $produit){
        
        //dd($produit);
        try {
             $oldCart = Session::has('cart') ? Session::get('cart') : null;
             $cart = new Pagner($oldCart);
             $cart->add($produit);
             Session::put('cart', $cart);
             //dd(Session::get('cart'));
             Session::put('topCart', $cart->items);
            //  dd(Session::get('topCart'));
             return response()->json([
                 'status_code'=>200,
                 'status_message'=>'Le Produit a ete ajouté au panier',
                 'data'=>$cart
             ]);
      
         }catch(Exception $e){
             return response()->json($e);
         }
 
     }
     public function afficherProduitsPanier(){

        $cart = Session()->get('cart');
        dd($cart);
        if($cart){
            return response()->json([
                'status_code'=>200,
                'status_message'=>'Liste des produits ajoutés au panier',
                'data'=>$cart
            ]);
        }else{
            return response()->json([
                'status_code'=>404,
                'status_message'=>'Le panier est vide',
                'data'=>null
            ]);
        }
    }
    
}
