<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertJson;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
   public function testUserCanRegisterSuccesfully()
   {
    $role=[
      'nom_role'=>'admin'
    ];
    $response = $this->postJson('api/role', $role);
    $response->assertStatus(201);

    $userData=[
        'nom'=>'Toure',
        'prenom'=>'Rokhaya',
        'email'=>'rokhaya@gmail.com',
        'telephone'=>'+221774065162',
        'role_id'=>1,
        'password'=>Hash::make('Passer11'),
       
    ];
    $response = $this->postJson('api/inscription', $userData);

    $response->assertStatus(201)
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
    $userData=[
        'nom'=>'Toure',
        'prenom'=>'Rokhaya',
        'email'=>'rokhaya@gmail.com',
        'telephone'=>'+221774065162',
        'role_id'=>1,
        'password'=>Hash::make('Passer11'),
       
    ];
    $response = $this->postJson('api/login', $userData);
    $response->assertStatus(201);
    $loginResponse=$this->post('api/login',[
        'email'=>$userData['email'],
        'password'=>$userData['password']

    ]);
    $loginResponse->assertStatus(201)
    ->assertJsonStructure([
        'status',
        'message',
    ]);
   }

public function testUserCanLogoutSucessfull()
{
   $user =User::where('email','rokhaya@gmail.com')->first();
   if($user){
    $this->fail('Utilisateur nom trouver');
   }
   $token=auth('api')->login($user);
   $respose=$this->withHeader()
}


    }
    
    

    

