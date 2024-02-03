<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ProduitTest extends TestCase
{
    use RefreshDatabase; 
    /**
     * A basic unit test example.
     */
    public function testAjoutProduit()
    {
        // Créer un rôle "admin"
        $roleAdmin = Role::create(['nom_role' => 'admin']);
        $adminUser = User::create([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'admin@example.com',
            'telephone' => '+221774065162',
            'role_id' =>  $roleAdmin->id,
            'password' => Hash::make('Passer11'),
        ]);
    
        // Utilisez l'API avec l'utilisateur authentifié
        $this->actingAs($adminUser, 'api');
    
        // Ajouter une catégorie
        $categorieData = Categorie::create(['nom_categories' => 'Nouvelle catégorie']);
        $response = $this->postJson('/api/AjoutCategorie', ['nom_categories' => 'Nouvelle catégorie']);
        $response->assertStatus(200);
    
        // Créer un rôle "agriculteur"
        $roleAgriculteur = Role::create(['nom_role' => 'agriculteur']);
        $agriculteurUser = User::create([
            'nom' => 'Agriculteur',
            'prenom' => 'Agriculteur',
            'email' => 'agriculteur@example.com',
            'telephone' => '+221774065162',
            'role_id' => $roleAgriculteur->id,
            'password' => Hash::make('Passer11'),
        ]);
    
        // Utilisez l'API avec l'utilisateur authentifié
        $this->actingAs($agriculteurUser, 'api');
    
        // Ajouter un produit
        $response = $this->postJson('/api/AjoutProduit', [
            'nom_produit' => 'Nouveau produit',
            'description' => 'Description du nouveau produit',
            'quantite' => 10,
            'prix' => 50,
            'categorie_id' =>  $categorieData->id ?? null, // Utilisez l'id de la catégorie créée
            'images' => 'C:\Users\simplon\Documents\SUNNU_MBAYE_MI\public\images\202401191737woman-8492233_1280.jpg',
        ]);
    
        // Assurez-vous que la réponse a le code de statut attendu
        $response->assertStatus(200);
    }
    
    


}
