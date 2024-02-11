<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\Role;
Use Illuminate\Testing\TestResponse;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\UserController;
use function PHPUnit\Framework\assertJson;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
     use RefreshDatabase;
   public function testUserCanRegisterSuccesfully()
   {
        $role=Role::create(["nom_role"=>"agriculteur"]);

    $userData=[
        'nom'=>'Toure',
        'prenom'=>'Rokhaya',
        'email'=>'rokhaya@gmail.com',
        'telephone'=>'+221774065162',
        'role_id'=>1,
        'password'=>Hash::make('Passer11'),
       
    ];
    $response = $this->postJson('api/inscription', $userData);

    $response->assertStatus(200)
    ->assertJsonStructure([
        'status',
        'message',
        'user',
        ]);
        // $this->assertDatabaseHas('users',[
        //  'nom'=>'Toure',
        // 'prenom'=>'Rokhaya',
        // 'email'=>'rokhaya@gmail.com',
        // 'telephone'=>'+221774065162',
        // 'role_id'=>1,
        // 'password'=>'Passer11'
        // ]);


   }
   public function testUserCanLoginSucessfull()
   {
        $role=Role::create(["nom_role"=>"agriculteur"]);
    $userData=[
        'nom'=>'Toure',
        'prenom'=>'Rokhaya',
        'email'=>'rokhaya@gmail.com',
        'telephone'=>'+221774065162',
        'role_id'=>1,
        'password'=>Hash::make('Passer11'),
       
    ];
    $response = $this->Json('post','api/login', $userData);
    $response->assertStatus(200);
//     $loginResponse=$this->post('api/login',[
//         'email'=>$userData['email'],
//         'password'=>$userData['password']

//     ]);
//     $loginResponse->assertStatus(200);
//     ->assertJsonStructure([
//         'status',
//         'message',
//     ]);
   }
public function testUserCanLogoutSuccessfully()
{
    // Effectuez d'abord le processus d'inscription et de connexion
    $role=Role::create(["nom_role"=>"agriculteur"]);
    $userData = [
        'nom' => 'Toure',
        'prenom' => 'Rokhaya',
        'email' => 'rokhaya18@gmail.com',
        'telephone' => '+221774065162',
        'role_id' => 1,
        'password' => Hash::make('Passer11'),
    ];
    $this->postJson('api/inscription', $userData)->assertStatus(201);

    $loginResponse = $this->post('api/login', [
        'email' => $userData['email'],
        'password' => $userData['password'],
    ])->assertStatus(200);

    $token = $loginResponse['token'];

    // Maintenant, effectuez la demande de logout avec le token JWT obtenu lors de la connexion
    $logoutResponse = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('api/logout');
    

    // Assurez-vous que la déconnexion a réussi avec un statut 200
    $logoutResponse->assertStatus(200);

    // Vérifiez également que l'utilisateur est bien déconnecté
    $this->assertGuest();
}



    }
    
    

    

