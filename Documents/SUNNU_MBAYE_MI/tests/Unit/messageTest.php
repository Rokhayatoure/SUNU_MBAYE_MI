<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Message;
use App\Mail\ResponseMail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MessageController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class messageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function testAjouterMessage()
    {
        $this->artisan('migrate:fresh');

        $data = [
            'nom' => 'John Doe',
            'email' => 'john@example.com',
            'telephone' => '123456789',
            'message' => 'Contenu du message',
        ];

        $response = $this->Json('post','api/ajouterMessage', $data);

        $response->assertStatus(200);
            
    }




    
    public function testListerMessages()
    {
        $this->artisan('migrate:fresh');



        $role=Role::create(["nom_role"=>"admin"]);
         // Créer un utilisateur manuellement
         $user = User::create([
             'nom' => 'ba',
             'prenom' => 'kya',
             'email' => 'bro@gmail.com',
             'telephone' => '+221774065162',
             'role_id' => 1,
             'password' => Hash::make('Passer11'),
         ]);
         $this->actingAs($user, 'api');
        $data = [
            'nom' => 'John Doe',
            'email' => 'john@example.com',
            'telephone' => '123456789',
            'message' => 'Contenu du message',
        ];


        $response = $this->Json('get','api/listerMessages');

        $response->assertStatus(200);
            
    }
    public function testVoirPlusMessage()
    {
        $this->artisan('migrate:fresh');

        $role = Role::create(["nom_role" => "admin"]);
        $user = User::create([
            'nom' => 'ba',
            'prenom' => 'kya',
            'email' => 'bro@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
        $this->actingAs($user, 'api');

        // Création d'un message dans la base de données
        $message = Message::create([
            'nom' => 'John Doe',
            'email' => 'john@example.com',
            'telephone' => '123456789',
            'message' => 'Contenu du message',
        ]);

        $response = $this->json('get', "api/voirplusmessage/{$message->id}");

        $response->assertStatus(200);
            
    }
    
    public function testReponse()
    {
        $this->artisan('migrate:fresh');
       

        $role = Role::create(["nom_role" => "admin"]);
        $user = User::create([
            'nom' => 'ba',
            'prenom' => 'kya',
            'email' => 'bro@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
        $this->actingAs($user, 'api');
        Mail::fake();

        $data = [
            'continue' => 'Contenu de la réponse',
            'email' => 'john@example.com',
        ];

        $response = $this->Json('post','api/reponse', $data);

        $response->assertStatus(200);
           
    }
}
