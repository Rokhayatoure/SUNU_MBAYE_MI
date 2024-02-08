<?php

namespace Tests\Unit;
 Use Tests\TestCase;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoriTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function testAjoutCategorie()
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

        $categorieData = ['nom_categories' => 'Nouvelle catégorie'];

        $response = $this->postJson('/api/AjoutCategorie', $categorieData);
        $response->assertStatus(200);
            // ->assertJson(['message' => 'Catégorie ajoutée avec succès']);
    }
    public function testModifieCategorie()
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

        $categorieData = ['nom_categories' => 'Nouvelle catégorie'];
        $response = $this->postJson('/api/AjoutCategorie', $categorieData);
    
        // Assurez-vous que la catégorie a été créée avec succès
        // $response->assertStatus(200);
        
        // Affichez la réponse JSON complète
        // dump($response->json());
    
        // $categorie = $response->json('categorie');
    
        // Assurez-vous que la catégorie a bien un id avant de tenter de la modifier
        $categorieId = 1;
        // $this->assertNotNull($categorieId, 'L\'ID de la catégorie est nul.');
    
        // Données de modification
        $modificationData = ['nom_categories' => 'Nouveau nom de catégorie'];
    
        // Envoyer la demande de modification de catégorie
        $response = $this->putJson("/api/modifieCategorie/{$categorieId}", $modificationData);
    
        // Vérifier la réponse
        $response->assertStatus(200);
        // ->assertJson(['message' => 'Catégorie modifiée avec succès']);
    }
    
    public function testSupprimeCategorie()
    {
        // Créer un rôle "admin"
        $role = ['nom_role' => 'admin'];
        $this->postJson('api/role', $role)->assertStatus(201);
      
        // Créer un utilisateur avec le rôle "admin"
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' => 'rokhaya@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
    
        $this->actingAs($user, 'api');
    
        // Créer une catégorie
        $categorieData = ['nom_categories' => 'Nouvelle catégorie'];
        $response = $this->postJson('/api/AjoutCategorie', $categorieData);
        // $response->assertStatus(200);
    
        // Récupérer l'ID de la catégorie créée
        // $categorie = $response->json('categorie');
         $categorieId = 1;
        // $this->assertNotNull($categorieId, 'L\'ID de la catégorie est nul.');
    
        // Envoyer la demande de suppression de catégorie
        $response = $this->actingAs($user, 'api')->deleteJson("/api/supCategorie/{$categorieId}");
    
        // Vérifier la réponse
        $response->assertStatus(200);
            // ->assertJson(['message' => 'Catégorie supprimée avec succès']);
    }
    }
