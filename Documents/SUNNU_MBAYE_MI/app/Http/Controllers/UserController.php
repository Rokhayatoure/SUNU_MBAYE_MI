<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;




class UserController extends Controller
{
    public function ajouterRole(Request $request)
    {
        $request->validate([
            'nom_role' => 'required',
        ]);
  
        $role = Role::create([
            'nom_role' => $request->nonRole,
        ]);
  
        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }


    //inscription
public function inscription(Request $request,Role $role){


    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
        'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
        'email' => ['required', 'email', 'unique:users,email'],
        'adresse' => ['required', 'string'],
        'contact' => ['required', 'string'],
        'date_naissance' => ['required'],
        'nom_role' => ['required', Role::in(['revendeur', 'agriculteur'])],
    ]);
    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    if ($request->role === 'revendeur') {
        $role = Role::where('nom_role', 'revendeur')->first();
        $user = new User([
            'name' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'date_naissance' => $request->date_naissance,
            'contact' => $request->contact,
             'sexe' => $request->sexe,
            'role_id' => $role->id,
        ]);
    
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('public/image'), $filename);
            $user->image = $filename;
        }
    
        $user->save();
    } else {
        $role = Role::where('nom_role', 'agriculteur')->first();
        $user = new User([
            'name' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'date_naissance' => $request->date_naissance,
            'contact' => $request->contact,
            'sexe' => $request->sexe,
            'role_id' => $role->id,
        ]);
    
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $user->image = $filename;
        }
    
        $user->save();
    }
    
    return response()->json(['message' => 'Utilisateur ajouté avec succès'], 201);
}



// loging

  public function login(Request $request)
    {

        // data validation
        $validator = Validator::make($request->all(), [
            "email" => "required|email",

        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // JWTAuth
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);
        if (!empty($token)) {
       $user = Auth::user();
            return response()->json([
                "status" => true,
                "message" => "utilisateur connecter avec succe",
                "token" => $token,
                'user'=>$user
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "details invalide"
        ]);
    }




    // logout un user
    public function logout(Request $request)
{
    Auth::logout();
    return response()->json([
        "status" => true,
        "message" => "utilisateur déconnecté avec succès"
    ]);
}

// update
public function updateUser(Request $request,$id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $user->name = $request->name;
    $user->prenom = $request->prenom;
    $user->adresse = $request->adresse;
    $user->date_naissance = $request->date_naissance;
    $user->contact = $request->contact;
    $user->sexe= $request->sexe;
    $user->password= $request->password;
   
  

    // Gérer la mise à jour de l'image si elle est fournie
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $file->move(public_path('images'), $filename);
        $user->image = $filename;
    }

    $user->save();

    return response()->json(['message' => 'Profil utilisateur mis à jour avec succès'], 200);
}

}
