<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnnonceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function ajoutAnnonce(Request $request)
    {
        $annonce = new Annonce([
            'titre' => $request->titre,
            'description' => $request->description,

        ]);
    
        // Gérer l'upload de l'image
        if ($request->hasFile('images')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $annonce->image = $filename;
        }
    
        $annonce->save();
    }


    public function modifieAnnonce(Request $request ,$id)
    {
        $annonce= Annonce::find($id);
        if (!$annonce) {
            return response()->json(['message' => 'Annnonce  non trouvé'], 404);
        }
      $annonce->titre = $request->titre;
        $annonce->description = $request->description;
    // Gérer la mise à jour de l'image si elle est fournie
    if ($request->hasFile('images')) {
        $file = $request->file('image');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $file->move(public_path('images'), $filename);
        $annonce->image = $filename;
    }

    $annonce->save();

    return response()->json(['message' => 'Profil utilisateur mis à jour avec succès'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function supAnnonce($id)
    {
        Annonce::find($id)->delete();
    return response()->json(['message' => 'Annonce supprimé avec succès'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Annonce $annonce)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annonce $annonce)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Annonce $annonce)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Annonce $annonce)
    {
        //
    }
}
