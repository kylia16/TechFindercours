<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // code_user : format "USR-XXXX" sans espace (le bothify avec espace pose problème)
            'code_user'     => 'USR-' . strtoupper($this->faker->unique()->bothify('????####')),
            'nom_user'      => $this->faker->lastName(),
            'prenom_user'   => $this->faker->firstName(),
            'login_user'    => $this->faker->unique()->userName(),
            'password_user' => bcrypt('password'), // mot de passe par défaut pour les tests
            'tel_user'      => $this->faker->phoneNumber(),
            'sexe_user'     => $this->faker->randomElement(['M', 'F']),
            'role_user'     => $this->faker->randomElement(['client', 'technicien']),
            'etat_user'     => $this->faker->randomElement(['actif', 'inactif', 'bloquer']),
        ];
    }

    // ─── STATES (états spéciaux pour les tests) ───────────────────────────────

    /**
     * State : un utilisateur de type "client" actif
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_user' => 'client',
            'etat_user' => 'actif',
        ]);
    }

    /**
     * State : un utilisateur de type "technicien" actif
     */
    public function technicien(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_user' => 'technicien',
            'etat_user' => 'actif',
        ]);
    }

    /**
     * State : un utilisateur de type "admin" actif
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_user' => 'admin',
            'etat_user' => 'actif',
        ]);
    }

    /**
     * State : un utilisateur bloqué (ne peut pas se connecter)
     */
    public function bloque(): static
    {
        return $this->state(fn (array $attributes) => [
            'etat_user' => 'bloquer',
        ]);
    }

    /**
     * State : un utilisateur inactif (en attente de validation)
     */
    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => [
            'etat_user' => 'inactif',
        ]);
    }
}
