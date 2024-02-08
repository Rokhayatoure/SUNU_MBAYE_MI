<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use OpenAi\Annotations as OA;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Documentation du projet SUNNU_MBAYE_MI",
 *     version="1.0.0",
 *     description="Documentation de l'API du projet SUNNU_MBAYE_MI",
 *     @OA\PathItem(path="/User")
 * )
 */


 /**
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */
 


class UserController extends Controller
{
     /**
 * Ajouter un nouveau rôle.
 *
 * @OA\Post(
 *     path="/api/ajouterRole",
 *     summary="Ajouter un rôle",
 *     tags={"Rôles"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"nom_role"},
 *                 @OA\Property(property="nom_role", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Rôle ajouté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Rôle ajouté avec succès"),
 *             @OA\Property(property="role", ref="#/components/schemas/Role")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */
    public function ajouterRole(Request $request)
    {
        $request->validate([
            'nom_role' => 'required',
        ]);
  
        $role = Role::create([
            'nom_role' => $request->nom_role,
        ]);
  
        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }


/**
 * Lister tous les rôles.
 *
 * @OA\Get(
 *     path="/api/listRole",
 *     summary="Lister tous les rôles",
 *     tags={"Rôles"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste de tous les rôles",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(
 *                 property="roles",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Role")
 *             )
 *         )
 *     )
 * )
 */

    public function listRole()
    {
       $role=Role::all();
       
    return response()->json(compact('role'), 200);
    }
/**
 * Inscrire un nouvel utilisateur.
 *
 * @OA\Post(
 *     path="/api/inscription",
 *     summary="Inscrire un nouvel utilisateur",
 *     tags={"Utilisateurs"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"nom", "prenom", "email", "telephone", "role_id", "password"},
 *                 @OA\Property(property="nom", type="string", example="John"),
 *                 @OA\Property(property="prenom", type="string", example="Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                 @OA\Property(property="telephone", type="string", example="+221771234567"),
 *                 @OA\Property(property="role_id", type="integer", example=1),
 *                 @OA\Property(property="password", type="string", example="password"),
 *                 @OA\Property(property="adresse", type="string", example="123 Rue de l'Exemple"),
 *                 @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
 *                 @OA\Property(property="sexe", type="string", example="M"),
 *                 @OA\Property(property="image", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur inscrit avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Utilisateur inscrit avec succès"),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */

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
        ],201);
    }
    

/**
 * Connecter l'utilisateur.
 *
 * @OA\Post(
 *     path="/api/login",
 *     summary="Connecter l'utilisateur",
 *     tags={"Utilisateurs"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur connecté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Utilisateur connecté avec succès"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Identifiants invalides"),
 *     @OA\Response(response=403, description="Compte bloqué"),
 *     @OA\Response(response=422, description="Erreur de validation"),
 *     @OA\Response(response=500, description="Erreur interne du serveur")
 * )
 */

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


    
   

/**
 * Déconnecter l'utilisateur.
 *
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Déconnecter l'utilisateur",
 *     tags={"Utilisateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur déconnecté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Utilisateur déconnecté avec succès")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé")
 * )
 */

    // logout un user
    public function logout(Request $request)
{
    Auth::logout();
    return response()->json([
        "status" => true,
        "message" => "utilisateur déconnecté avec succès"
    ],200);
}



/**
 * Mettre à jour un utilisateur.
 *
 * @OA\Put(
 *     path="/api/updateUser/{id}",
 *     summary="Mettre à jour un utilisateur",
 *     tags={"Utilisateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de l'utilisateur à mettre à jour",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(property="nom", type="string"),
 *                 @OA\Property(property="prenom", type="string"),
 *                 @OA\Property(property="adresse", type="string"),
 *                 @OA\Property(property="date_naissance", type="string", format="date"),
 *                 @OA\Property(property="telephone", type="string"),
 *                 @OA\Property(property="sexe", type="string", enum={"Homme", "Femme"}),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string", format="password"),
 *                 @OA\Property(property="role_id", type="integer"),
 *                 @OA\Property(property="image", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur mis à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Modifier avec succès"),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=403, description="Vous ne pouvez pas faire cette action"),
 *     @OA\Response(response=404, description="Utilisateur non trouvé"),
 *     @OA\Response(response=422, description="Erreur de validation")
 * )
 */
public function updateUser(Request $request,$id)
{
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
    $user = User::find($id);

if (!$user) {
    return response()->json(['message' => 'Utilisateur non trouvé'], 404);
}
if ($user->user_id !==  Auth::guard('api')->user()->id){
    return response()->json([
        "status" => false,
        "message" => "vous ne pouvez pas faire cette action "
    ],403);
}

$user->nom = $request->nom;
$user->prenom = $request->prenom;
$user->adresse = $request->adresse;
$user->date_naissance = $request->date_naissance;
$user->telephone = $request->telephone;
$user->sexe = $request->sexe;
$user->email = $request->email;
$user->password = $request->password;

// Gérer la mise à jour de l'image si elle est fournie
if ($request->hasFile('image')) {
    $file = $request->file('image');
    $filename = date('YmdHi') . $file->getClientOriginalName();
    $file->move(public_path('images'), $filename);
    $user->image = $filename;
}

$user->save();

return response()->json([
    "status" => true,
    "message" => "Modifier avec succès",
    'user' => $user
]);
}


/**
 * Lister tous les utilisateurs (sauf les administrateurs).
 *
 * @OA\Get(
 *     path="/api/listeUser",
 *     summary="Lister tous les utilisateurs (sauf les administrateurs)",
 *     tags={"Utilisateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Liste de tous les utilisateurs (sauf les administrateurs)",
 *         @OA\JsonContent(
 *             @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/User"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé")
 * )
 */


public function listeUser()
{
    // Récupérer l'ID du rôle "admin"
    $adminRoleId = DB::table('roles')->where('nom_role', 'admin')->value('id');

    // Récupérer tous les utilisateurs sauf l'admin
    $users = User::where('role_id', '!=', $adminRoleId)->get();

    return response()->json(compact('users'), 200);
}


/**
 * Débloquer un utilisateur.
 *
 * @param int $id ID de l'utilisateur à débloquer
 * 
 * @OA\delete(
 *     path="/api/debloquerUser/{id}",
 *     summary="Débloquer un utilisateur",
 *     tags={"Utilisateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur à débloquer",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="L'utilisateur a été débloqué avec succès."),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=404, description="Utilisateur non trouvé")
 * )
 */
public function debloquerUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    // Débloquer l'utilisateur
    $user->is_blocked = false;
    $user->save();

    return response()->json([
        'status' => true,
        'message' => "L'utilisateur a été débloqué avec succès.",
        
        'user' => $user,
    ]);
}


/**
 * Bloquer  un utilisateur.
 *
 * @param int $id ID de l'utilisateur à bloquer
 * 
 * @OA\delete(
 *     path="/api/BloquerUser/{id}",
 *     summary="Bloquer ou débloquer un utilisateur",
 *     tags={"Utilisateurs"},
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur à bloquer/débloquer",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="L'utilisateur a été bloqué/débloqué avec succès"),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Non autorisé"),
 *     @OA\Response(response=403, description="Vous ne pouvez pas bloquer votre propre compte"),
 *     @OA\Response(response=404, description="Utilisateur non trouvé")
 * )
 */
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
