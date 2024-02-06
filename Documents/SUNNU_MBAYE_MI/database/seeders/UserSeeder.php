<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     
        // Création d'un utilisateur admin
        $adminRole = Role::where('nom_role', 'admin')->first();

        if ($adminRole) {
            User::create([
                'nom' => 'Coly',
                'prenom' => 'admin',
                'email' => 'admin@gmail.com',
                'telephone' => '+221774003030',
                'password' => bcrypt('admin@123'),
                'role_id' => $adminRole->id,
            ]);
        } else {
            $this->command->info('Le rôle "admin" n\'a pas été trouvé. Assurez-vous qu\'il existe dans la table des rôles.');
        }
    }
}
    
