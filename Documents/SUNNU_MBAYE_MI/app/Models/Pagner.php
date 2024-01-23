<?php

namespace App\Models;


class Pagner 
{
    
        public $items = null;
        public $totalQte = 0;
        public $totalPrix = 0;
    
        public function __construct($oldCart){
            if($oldCart){
                $this->items = $oldCart->items;// sa prend toutes les produit
                $this->totalQte = $oldCart->totalQte;
                $this->totalPrix = $oldCart->totalPrix;
            }
        }
    
        public function add($item){
            $ajoutPanier = [
                'quantiter'=>0,
                'id'=>0,
                'nom_produit'=>$item->nom_produit,
                'description'=>$item->description,
                'prix'=>$item->prix,
                'image'=>$item->image,
                'item'=>$item
            ];
    
            if($this->items){
                if(array_key_exists($item->id, $this->items)){
                    $ajoutPanier = $this->items[$item->id];
                }
            }
    
            $ajoutPanier['quantiter']++;
            $ajoutPanier['id']= $item->id;
            $ajoutPanier['nom_produit']= $item->nom_produit;
            $ajoutPanier['description']= $item->description;
            $ajoutPanier['prix']= $item->prix;
            $this->totalQte++;
            $this->totalPrix += $item->prix;
            $this->items[$item->id] = $ajoutPanier;
        }
}
