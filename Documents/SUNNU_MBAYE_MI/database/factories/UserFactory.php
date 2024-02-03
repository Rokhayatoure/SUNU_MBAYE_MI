<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->firstName,
            'prenom' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'contact' => $this->faker->phoneNumber,
            'sexe' => $this->faker->randomElement(['Homme', 'Femme']),
            'profile' => $this->faker->sentence,
            'date_naissance' => $this->faker->date,
            'adresse' => $this->faker->address,
            'password' => Hash::make('password'), // Utilisez Hash::make pour hasher le mot de passe
            'role_id' => function () {
                return Role::factory()->create()->id;
            },
            'image' => $this->faker->imageUrl(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
