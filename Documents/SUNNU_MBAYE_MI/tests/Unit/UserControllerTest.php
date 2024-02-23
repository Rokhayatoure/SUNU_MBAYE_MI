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
    $this->artisan('migrate:fresh');

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
   public function testConnexionUtilisateur()
   {
    $this->artisan('migrate:fresh');

        $role=Role::create(["nom_role"=>"agriculteur"]);
        $password='Passer11';
    $userData= User::create([

        'nom'=>'Toure',
        'prenom'=>'Rokhaya',
        'email'=>'khe@gmail.com',
        'telephone'=>'+221774065162',
        'role_id'=>1,
        'password'=>Hash::make( $password),
       
    ]);

    
    $response=$this->json('POST','api/login',[

        'email'=>$userData->email,
        'password'=>$password
       

    ]);
    $response->assertStatus(200)
    ->assertJson([
        "message" => "Utilisateur connecté avec succès",
    ]);
   }
public function testUserCanLogoutSuccessfully()
{
    $this->artisan('migrate:fresh');

    // Effectuez d'abord le processus d'inscription et de connexion
     $role=Role::create(["nom_role"=>"agriculteur"]);
     $password ='Passer11';
    $user = User::create([
        'nom' => 'Toure',
        'prenom' => 'Rokhaya',
        'email' =>'papa@gmail.com',
        'telephone' => '+221774065162',
        'role_id' => 1,
        'password' => Hash::make('Passer11'),
    ]);
    $token = JWTAuth::fromUser($user);
$this->withHeader('Authorization',"Bearer " .$token);
    $response=$this->post('api/logout');
    $response->assertStatus(200);
}



    }
    
    

    

