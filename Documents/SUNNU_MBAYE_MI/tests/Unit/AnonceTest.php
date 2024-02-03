<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Annonce;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnonceTest extends TestCase
{
    use RefreshDatabase;

    public function testAjoutAnnonce()
    {
        // Storage::fake('images');
        $role=[
                  'nom_role'=>'admin'
                ];
                $response = $this->postJson('api/role', $role);
                $response->assertStatus(201);
            
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' => 'rokhaya@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);

        $this->actingAs($user, 'api');

        // Créer des données pour l'annonce
        $annonceData = [
            'titre' => 'Nouvelle annonce',
            'description' => 'Description de la nouvelle annonce',
            // 'images' => UploadedFile::fake()->image('annonce.jpg'),
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
        // Créer un rôle manuellement
        $role = [
            'nom_role' => 'admin'
        ];
        $response = $this->postJson('api/role', $role);
        $response->assertStatus(201);
    
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' => 'rokhaya@gmail.com',
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
    
        $response = $this->putJson("/api/modifierAnnonce/{$annonce->id}", $modificationData);
    
        $response->assertStatus(200)
            ->assertJson([
                'status' => $response->json('status'),
                'message' => $response->json('message'),
                 'annonce' =>$response->json('annonce')
                
                
            ]);
    
        // Assurez-vous que l'image existe dans le stockage simulé
        // 
    }
    


    public function testListAnnonce()
    {
        
        $role=[
            'nom_role'=>'admin'
          ];
          $response = $this->postJson('api/role', $role);
          $response->assertStatus(201);
      
  // Créer un utilisateur manuellement
  $user = User::create([
      'nom' => 'Toure',
      'prenom' => 'Rokhaya',
      'email' => 'rokhaya@gmail.com',
      'telephone' => '+221774065162',
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
//   $response = $this->postJson('/api/ajoutAnnonce', $annonceData);
        $response = $this->getJson('/api/listAnnonce');

        $response->assertStatus(200);
       
    }

    public function testSupprimerAnnonce()
    {
        // Créer un utilisateur
        $role = [
            'nom_role' => 'admin'
        ];
        $response = $this->postJson('api/role', $role);
        $response->assertStatus(201);
    
        // Créer un utilisateur manuellement
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' => 'rokhaya@gmail.com',
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
}




