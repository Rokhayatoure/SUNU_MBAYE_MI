<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['nom_role' => 'admin']);
        Role::create(['nom_role' => 'utilisateur']);
        Role::create(['nom_role' => 'agriculteur']);
    }
}
