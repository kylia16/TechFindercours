<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\Competence;
use App\Models\User_Competence;
use Illuminate\Database\Seeder;

class UserCompetenceSeeder extends Seeder
{
    /**
     * Assigne des compétences aux techniciens.
     *
     * Règle métier : seuls les techniciens ont des compétences.
     * Chaque technicien reçoit entre 1 et 5 compétences aléatoires.
     */
    public function run(): void
    {
        $techniciens = Utilisateur::where('role_user', 'technicien')->get();
        $competences = Competence::all();

        if ($competences->isEmpty()) {
            $this->command->warn('Aucune compétence trouvée. Lance CompetenceSeeder d\'abord.');
            return;
        }

        foreach ($techniciens as $technicien) {
            // Nombre aléatoire de compétences par technicien (entre 1 et 5)
            $nombreCompetences = rand(1, min(5, $competences->count()));

            // On pioche des compétences au hasard sans doublon
            $competencesAléatoires = $competences->random($nombreCompetences);

            foreach ($competencesAléatoires as $competence) {
                // On vérifie qu'on n'insère pas de doublon (au cas où le seeder tourne 2 fois)
                User_Competence::firstOrCreate([
                    'code_user' => $technicien->code_user,
                    'code_comp' => $competence->code_comp,
                ]);
            }
        }
    }
}
