<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class User extends Controller
{
    public function ajouterRole(Request $request)
    {
        $request->validate([
            'nom_role' => 'required',
        ]);
  
        $role = Role::create([
            'nonRole' => $request->nonRole,
        ]);
  
        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }
    
}
