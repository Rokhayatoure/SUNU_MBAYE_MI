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
use OpenAi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Documentation du projet SUNNU_MBAYE_MI",
 *     version="1.0.0",
 *     description="Documentation de l'API du projet SUNNU_MBAYE_MI",
 *     @OA\PathItem(path="/User")
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
            'nom_role' => $request->nonRole,
        ]);
  
        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }

  /**
     * @OA\Post(
     *     path="/api/inscription",
     *     summary="inscription",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="adresse", type="string")
     *         )
     *     ),
     *     @OA\Response(
 *         response=201,
 *         description="Utilisateur ajouté avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Utilisateur ajouté avec succès")
 *         )
 *     ),
     *     @OA\Response(response="422", description="Erreur de validation")
     * )
     */

     public function inscription(Request $request, Role $role) {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telephone' => ['required', 'string'],
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
        ]);
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
            "message" => "details invalide"
        ]);
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
    ]);
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

    return response()->json([
        "status" => true,
        "message" => "modifier avec success avec succes ",
        
        'user'=>$user
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
    $user=User::all();
    return response()->json(compact('user'), 200);
}

}
