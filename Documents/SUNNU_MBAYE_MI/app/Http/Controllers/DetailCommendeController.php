<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Commende;
use Illuminate\Http\Request;
use App\Models\DetailCommende;
use Illuminate\Routing\Controller;

class DetailCommendeController extends Controller
{
    public function effectuerCommande(Request $request ,Produit $produit_id)
    {
        // Récupérez le produit par son ID
        $produit = Produit::findOrFail($produit_id);
        $commende = new Commende();
        $commende->livraison = 'En_cours';
        $commende->produit_id = $produit->id;
        $commende->save();
        // $detailCommende = new DetailCommende();
      
        // $detailCommende->commende_id = $commende->id;
        // $detailCommende->save();

}

}
