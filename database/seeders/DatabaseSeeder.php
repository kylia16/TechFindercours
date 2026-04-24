<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * L'ordre est IMPORTANT à cause des clés étrangères :
     * 1. Competences (pas de dépendance)
     * 2. Utilisateurs (pas de dépendance)
     * 3. UserCompetences (dépend de Utilisateur + Competence)
     * 4. Interventions (dépend de Utilisateur + Competence)
     */
    public function run(): void
    {
        $this->call([
            CompetenceSeeder::class,
            UtilisateurSeeder::class,
            UserCompetenceSeeder::class,
            InterventionSeeder::class,
        ]);
    }
}
