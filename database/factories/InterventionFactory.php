<?php

namespace Database\Factories;

use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Competence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intervention>
 */
class InterventionFactory extends Factory
{
    protected $model = Intervention::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // On récupère des utilisateurs existants selon leur rôle
        $client      = Utilisateur::where('role_user', 'client')->inRandomOrder()->first()
                       ?? Utilisateur::factory()->client()->create();

        $technicien  = Utilisateur::where('role_user', 'technicien')->inRandomOrder()->first()
                       ?? Utilisateur::factory()->technicien()->create();

        $competence  = Competence::inRandomOrder()->first()
                       ?? Competence::factory()->create();

        return [
            'date_int'         => $this->faker->dateTimeBetween('-6 months', 'now'),
            'note_int'         => $this->faker->numberBetween(0, 20),
            'commentaire_int'  => $this->faker->optional(0.7)->sentence(), // 70% de chance d'avoir un commentaire
            'code_user_client' => $client->code_user,
            'code_user_techn'  => $technicien->code_user,
            'code_comp'        => $competence->code_comp,
        ];
    }

    /**
     * State : intervention bien notée (>= 15/20)
     */
    public function bienNotee(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_int' => $this->faker->numberBetween(15, 20),
        ]);
    }

    /**
     * State : intervention mal notée (< 10/20)
     */
    public function malNotee(): static
    {
        return $this->state(fn (array $attributes) => [
            'note_int' => $this->faker->numberBetween(0, 9),
        ]);
    }

    /**
     * State : intervention sans commentaire
     */
    public function sansCommentaire(): static
    {
        return $this->state(fn (array $attributes) => [
            'commentaire_int' => null,
        ]);
    }
}
