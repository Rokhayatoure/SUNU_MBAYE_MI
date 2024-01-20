<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listeCategorie(Request $request)
    {
        $categorie=Categorie::all();
        return response()->json(compact('categorie'), 200);
      
    }

    /**
     * Show the form for creating a new resource.
     */
    public function AjoutCategorie(Request $request ,$id)
    {

        $categorie= new Categorie([
            'nom_categories' => $request->nom_categories,
            'description' => $request->description,
        ]);
        $categorie->save();
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function modifieCategorie(Request $request, $id)
    {
        
        $categorie= Categorie::find($id);
        if (!$categorie) {
            return response()->json(['message' => 'Categorie  non trouver'], 404);
        }

        $categorie= new Categorie([
            'nom_categories' => $request->nom_categories,
            'description' => $request->description,
        ]);
        $categorie->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Categorie::find($id)->delete();
    return response()->json(['message' => 'categorie supprimé avec succès'], 200);
    
    }
}
