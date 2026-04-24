<?php

namespace Database\Seeders;

use App\Models\Intervention;
use App\Models\Utilisateur;
use App\Models\Competence;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InterventionSeeder extends Seeder
{
    /**
     * Crée des interventions réalistes en base de données.
     */
    public function run(): void
    {
        $clients     = Utilisateur::where('role_user', 'client')->get();
        $techniciens = Utilisateur::where('role_user', 'technicien')->get();
        $competences = Competence::all();

        if ($clients->isEmpty() || $techniciens->isEmpty() || $competences->isEmpty()) {
            $this->command->warn('Données manquantes : lance d\'abord UtilisateurSeeder et CompetenceSeeder.');
            return;
        }

        // ─── Interventions fixes (données connues pour les tests) ─────────────
        Intervention::create([
            'date_int'         => Carbon::now()->subDays(10),
            'note_int'         => 18,
            'commentaire_int'  => 'Excellent service, technicien très professionnel.',
            'code_user_client' => $clients->first()->code_user,
            'code_user_techn'  => $techniciens->first()->code_user,
            'code_comp'        => $competences->first()->code_comp,
        ]);

        Intervention::create([
            'date_int'         => Carbon::now()->subDays(5),
            'note_int'         => 8,
            'commentaire_int'  => 'Service moyen, le problème n\'est pas totalement résolu.',
            'code_user_client' => $clients->get(1)->code_user,
            'code_user_techn'  => $techniciens->get(1)->code_user,
            'code_comp'        => $competences->get(1)->code_comp,
        ]);

        // ─── Génération aléatoire en masse ────────────────────────────────────
        for ($i = 0; $i < 50; $i++) {
            $client     = $clients->random();
            $technicien = $techniciens->random();
            $competence = $competences->random();

            Intervention::create([
                'date_int'         => Carbon::now()->subDays(rand(1, 180)),
                'note_int'         => rand(0, 20),
                'commentaire_int'  => rand(0, 1) ? fake()->sentence() : null,
                'code_user_client' => $client->code_user,
                'code_user_techn'  => $technicien->code_user,
                'code_comp'        => $competence->code_comp,
            ]);
        }
    }
}
