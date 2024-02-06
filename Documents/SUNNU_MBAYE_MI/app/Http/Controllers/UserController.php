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
     * @OA\Post(
     *     path="/api/role",
     *     summary="Ajouter un rôle",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom_role", type="string")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="Rôle ajouté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rôle ajouté avec succès"),
     *             @OA\Property(property="role", type="object", ref="#/components/schemas/Role")
     *         )
     *     ),
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




    public function listRole()
    {
       $role=Role::all();
       
    return response()->json(compact('role'), 200);
    }
/**
 * @OA\Post(
 *     path="/api/inscription",
 *     summary="Inscription d'un utilisateur",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom", type="string"),
 *                 @OA\Property(property="prenom", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="telephone", type="string"),
 *                 @OA\Property(property="role_id", type="integer"),
 *                 @OA\Property(property="password", type="string", format="password"),
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
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Erreur de validation"),
 *     @OA\Response(response=500, description="Erreur interne du serveur")
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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Connecter un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur connecté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Utilisateur connecté avec succès"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Détails invalides")
     * )
     */
// loging

  public function login(Request $request)
    {

        // data validate 
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
             "password" => "required|"

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
            "message" => "details invalide",
            
        ],201);
    }


 /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Déconnecter un utilisateur",
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur déconnecté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Utilisateur déconnecté avec succès")
     *         )
     *     )
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
     * @OA\Post(
     *     path="/api/updateUser/{id}",
     *     summary="Mettre à jour le profil d'un utilisateur",
     *     @OA\Parameter(
     *      name="id",
     *  * )
     */
    
// update
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
 * @OA\Get(
 *     path="/api/listeUser",
 *     summary="Récupère la liste de tous les utilisateurs.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des utilisateurs",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         ),
 *     ),
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

public function BloquerUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
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
