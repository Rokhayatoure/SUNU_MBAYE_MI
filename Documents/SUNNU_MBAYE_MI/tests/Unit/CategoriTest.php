<?php

namespace Tests\Unit;
 Use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Categorie;
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
        
        $role=Role::create(["nom_role"=>"admin"]);
          
      
  // Créer un utilisateur manuellement
  $user = User::create([
      'nom' => 'Toure',
      'prenom' => 'Rokhaya',
      'email' => 'MAMY@gmail.com',
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
//     public function testModifieCategorie()
//     {


//         $role=Role::create(["nom_role"=>"admin"]);
          
      
//   // Créer un utilisateur manuellement
//   $user = User::create([
//       'nom' => 'Toure',
//       'prenom' => 'Rokhaya',
//       'email' => 'aida@gmail.com',
//       'telephone' => '+221774065162',
//       'role_id' => 1,
//       'password' => Hash::make('Passer11'),
//   ]);
//   $categorie = Categorie::create([
//     'nom_categories' => 'Ancienne catégorie',
// ]);

//  $modificationData = ['nom_categories' => 'Nouveau nom de catégorie'];
// // dd($modificationData);
//   // Envoyer la demande de modification de catégorie
//   $response = $this->json('PUT',"/api/modifieCategorie/{$categorie}", $modificationData);

//   // Vérifier que la réponse est un succès (status code 200)
//   $response->assertStatus(200);
      
//     }
    
    public function testSupprimeCategorie()
    {
        // Créer un rôle "admin"
        
        $role=Role::create(["nom_role"=>"admin"]);
      
        // Créer un utilisateur avec le rôle "admin"
        $user = User::create([
            'nom' => 'Toure',
            'prenom' => 'Rokhaya',
            'email' =>'papa@gmail.com',
            'telephone' => '+221774065162',
            'role_id' => 1,
            'password' => Hash::make('Passer11'),
        ]);
    
        $this->actingAs($user, 'api');
    
        // Créer une catégorie
        $categorieData = ['nom_categories' => 'Nouvelle catégorie'];
        $response = $this->postJson('/api/AjoutCategorie', $categorieData);
        
         $categorieId = 1;
      
    
        // Envoyer la demande de suppression de catégorie
        $response = $this->actingAs($user, 'api')->deleteJson("/api/supCategorie/{$categorieId}");
    
        // Vérifier la réponse
        $response->assertStatus(200);
            // ->assertJson(['message' => 'Catégorie supprimée avec succès']);
    }
    }
