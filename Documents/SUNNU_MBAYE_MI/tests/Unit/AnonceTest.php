<?php

namespace Tests\Unit;


use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Annonce;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnonceTest extends TestCase
{
    //  use RefreshDatabase;

    public function testAjoutAnnonce()
    {
        
        $this->artisan('migrate:fresh');
        $role=Role::create(["nom_role"=>"agriculteur"]);
           
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' => 'rokhy@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);

        $this->actingAs($user, 'api');

        // Créer des données pour l'annonce
        $annonceData = [
            'titre' => 'Nouvelle annonce',
            'description' => 'Description de la nouvelle annonce',
            
        ];

        // Envoyer la demande d'ajout d'annonce
        $response = $this->postJson('/api/ajoutAnnonce', $annonceData);

        // Assurez-vous que la réponse est correcte
        $response->assertStatus(200)
            ->assertJson([
                'status' => $response->json('status'),
                'message' => $response->json('message'),
                 'annonce' =>$response->json('annonce')
                
            ]);

        // Assurez-vous que l'image existe dans le stockage simulé
        
    } 
    


    public function testModifierAnnonce()
    {
        $this->artisan('migrate:fresh');

        // Créer un rôle manuellement
        $role=Role::create(["nom_role"=>"admin"]);
    
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'keita',
            'prenom' => 'baba',
            'email' => 'baba@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
    
        $this->actingAs($user, 'api');//verifie si l'itulisateur itulise les gardes
    
        // Créer une annonce manuellement
        $annonce = Annonce::create([
            'titre' => 'Nouvelle annonce',
            'description' => 'Description de la nouvelle annonce',
            'user_id' => $user->id,
        ]);
    
        $modificationData = [
            'titre' => 'Nouveau titre',
            'description' => 'Nouvelle description',
            'images' => 'C:\Users\simplon\Documents\SUNNU_MBAYE_MI\public\images\202401191737woman-8492233_1280.jpg',
        ];
    
        $response = $this->postJson("/api/modifierAnnonce/{$annonce->id}", $modificationData);
    
        $response->assertStatus(200);
            // ->assertJson([
            //     'status' => $response->json('status'),
            //     'message' => $response->json('message'),
            //      'annonce' =>$response->json('annonce')
                
                
            // ]);
    
        // Assurez-vous que l'image existe dans le stockage simulé
        
    }
    


    public function testListAnnonce()
    {
        $this->artisan('migrate:fresh');
        
        $role=Role::create(["nom_role"=>"admin"]);
      
  // Créer un utilisateur manuellement
  $user = User::create([
      'nom' => 'colly',
      'prenom' => 'dieye',
      'email' => 'dieye@gmail.com',
      'telephone' => '+221764890909',
      'role_id' => 1,
      'password' => Hash::make('Passer11'),
  ]);

  $this->actingAs($user, 'api');

  // Créer des données pour l'annonce
  $annonceData = [
      'titre' => 'Nouvelle annonce',
      'description' => 'Description de la nouvelle annonce',
       'images' => 'C:\Users\simplon\Documents\SUNNU_MBAYE_MI\public\images\202401191737woman-8492233_1280.jpg',
  ];

        $response = $this->getJson('/api/listAnnonce');

        $response->assertStatus(200);
       
    }

    public function testSupprimerAnnonce()
    { 
        $this->artisan('migrate:fresh');

        // Créer un utilisateur
        $role=Role::create(["nom_role"=>"agriculteur"]);
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'ba',
            'prenom' => 'kya',
            'email' => 'kya@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
    
        $this->actingAs($user, 'api');//verifie si l'itulisateur itulise les gardes
    
        // Créer une annonce manuellement
        $annonce = Annonce::create([
            'titre' => 'Nouvelle annonce',
            'description' => 'Description de la nouvelle annonce',
            'user_id' => $user->id,
        ]);

        // Authentifier l'utilisateur
        $this->actingAs($user, 'api');

        // Appeler la route de suppression de l'annonce
        $response = $this->deleteJson("/api/supAnnonce/{$annonce->id}");

        // Vérifier que la réponse est correcte
        $response->assertStatus(200);
                //  ->assertJson([
                //      'status' => true,
                //      'message' => 'annonce supprimée avec succès',
                //      'annonce' => $annonce->toArray(),
                //  ]);

        // Vérifier que l'annonce a été supprimée de la base de données
        $this->assertDatabaseMissing('annonces', ['id' => $annonce->id]);

    }



public function testAnnoncePublieeAvecSucces()
    {
        $this->artisan('migrate:fresh');

         // Créer un utilisateur
         $role=Role::create(["nom_role"=>"agriculteur"]);
         // Créer un utilisateur manuellement
         $user = User::create([
             'nom' => 'ba',
             'prenom' => 'kya',
             'email' => 'kya@gmail.com',
             'telephone' => '+221774065162',
             'role_id' => 1,
             'password' => Hash::make('Passer11'),
         ]);
         $role=Role::create(["nom_role"=>"admin"]);
         // Créer un utilisateur manuellement
         $userAd = User::create([
             'nom' => 'ba',
             'prenom' => 'kya',
             'email' => 'ad@gmail.com',
             'telephone' => '+221774065162',
             'role_id' => 2,
             'password' => Hash::make('Passer11'),
         ]);

          // Simuler la connexion de l'utilisateur
        $this->actingAs($user, 'api');
        // Créer une annonce
        $annonce = Annonce::create([
            'titre' => 'Nouvelle annonce',
            'description' => 'Description de la nouvelle annonce',
            'user_id' => $user->id,
            "est_publier"=>1,
        ]);


        // Simuler la connexion de l'utilisateur
        $this->actingAs($userAd, 'api');
        

        // Appeler la fonction publierAnnonce avec l'ID de l'annonce
        $response = $this->json('get',"api/publierAnnonce/{$annonce->id}");
    
        // Vérifier que la réponse indique que l'annonce a été publiée avec succès
        $response->assertStatus(200);
        
    }



}




