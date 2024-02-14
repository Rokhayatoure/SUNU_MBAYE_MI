<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use OpenAi\Annotations as OA;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
            'nom_role' => $request->nom_role,
        ]);
  
        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 200);
    }




    public function listRole()
    {
       $role=Role::all();
       
    return response()->json(compact('role'), 200);
    }


     public function inscription(Request $request, Role $role) {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telephone' => ['required', 'string', 'regex:/^(\+221|221)?[76|77|78|70|33]\d{8}$/'],
            'role_id' => ['required','integer',],
            'password' => ['required', 'string', 'min:8'],

        ]); 

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        
        $user = new User([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'date_naissance' => $request->date_naissance,
            'telephone' => $request->telephone,
            'sexe' => $request->sexe,
            'role_id' => $request->role_id
          
        ]);
    
        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $user->image = $filename;
        }
    
        $user->save();
    
        return response()->json([
            "status" => true,
            "message" => "utilisateur connecter inscrit avec succes ",
            'user'=>$user
        ],200);
    }
    

public function login(Request $request)
{
    try {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Authentification de l'utilisateur
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                "status" => false,
                "message" => "Identifiants invalides",
            ], 401);
        }

        // Récupération de l'utilisateur
        $user = Auth::user();

        // Vérification si l'utilisateur est bloqué
        if ($user && $user->est_bloquer) {
            return response()->json([
                "status" => false,
                "message" => "Votre compte est bloqué. Veuillez contacter l'administrateur.",
            ], 403);
        }

        // Génération du token JWT
        return response()->json([
            "status" => true,
            "message" => "Utilisateur connecté avec succès",
            "token" => $token,
            'user' => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => "Une erreur s'est produite lors de la connexion.",
            "error" => $e->getMessage()
        ], 500);
    }
}


    
   


    // logout un user
    public function logout(Request $request)
{
    Auth::logout();
    return response()->json([
        "status" => true,
        "message" => "utilisateur déconnecté avec succès"
    ],200);
}




public function updateUser(Request $request,$id)
{

    $user = User::find($id);
    
    // dd($user);
if (!$user) {
    return response()->json(['message' => 'Utilisateur non trouvé'], 404);
}
$utilisateurconnecter = Auth::guard('api')->user();

if ($user->id !== $utilisateurconnecter->id){

    return response()->json([
        "status" => false,
        "message" => "vous ne pouvez pas faire cette action "
    ],403);
} 

$validator = Validator::make($request->all(), [
        'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
        'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
        'email' => ['required', 'email',Rule::unique('users')->ignore($user->id)],
        'telephone' => ['required', 'string', 'regex:/^(\+221|221)?[76|77|78|70|33]\d{8}$/'],
        'role_id' => ['required','integer',],
        'password' => ['required', 'string', 'min:8'],

    ]); 

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
   
$user->nom = $request->nom;
$user->prenom = $request->prenom;
$user->adresse = $request->adresse;
$user->date_naissance = $request->date_naissance;
$user->telephone = $request->telephone;
$user->sexe = $request->sexe;
$user->email = $request->email;
$user->password =  Hash::make($request->password);

// Gérer la mise à jour de l'image si elle est fournie
if ($request->hasFile('image')) {
    $file = $request->file('image');
    $filename = date('YmdHi') . $file->getClientOriginalName();
    $file->move(public_path('images'), $filename);
    $user->image = $filename;
}

$user->update();

return response()->json([
    "status" => true,
    "message" => "Modifier avec succès",
    'user' => $user
]);
}




public function listeUser()
{
    // Récupérer l'ID du rôle "admin"
    $adminRoleId = DB::table('roles')->where('nom_role', 'admin')->value('id');

    // Récupérer tous les utilisateurs sauf l'admin
    $users = User::where('role_id', '!=', $adminRoleId)->get();

    return response()->json(compact('users'), 200);
}


public function debloquerUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    // Débloquer l'utilisateur
    $user->est_bloquer= false;
    $user->save();

    return response()->json([
        'status' => true,
        'message' => "L'utilisateur a été débloqué avec succès.",
        
        'user' => $user,
    ]);
}



public function BloquerUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    $admin = Auth::user();
    if ($admin->id === $user->id) {
        return response()->json(['message' => 'Vous ne pouvez pas vous bloquer vous-même.'], 403);
    }

    // Inverser l'état de blocage
    $user->est_bloquer= !$user->est_bloquer;
    $user->save();

    $status = $user->est_bloquer ? 'bloqué' : 'débloqué';

    return response()->json([
        'status' => true,
        'message' => "L'utilisateur a été $status avec succès.",
        'user' => $user,
    ]);
}



}
