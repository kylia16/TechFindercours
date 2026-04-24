<?php

namespace Database\Seeders;

use App\Models\Competence;
use Illuminate\Database\Seeder;

class CompetenceSeeder extends Seeder
{
    /**
     * Crée 20 compétences techniques réalistes en base de données.
     */
    public function run(): void
    {
        // Compétences fixes avec des données métier réelles
        $competences = [
            ['label_comp' => 'Réparation PC',              'description_comp' => 'Diagnostic et réparation de pannes matérielles et logicielles.'],
            ['label_comp' => 'Installation réseau',        'description_comp' => 'Configuration de réseaux Wi-Fi et filaires (routeurs, switches).'],
            ['label_comp' => 'Maintenance imprimante',     'description_comp' => 'Entretien et dépannage d\'imprimantes laser et jet d\'encre.'],
            ['label_comp' => 'Sécurité informatique',      'description_comp' => 'Mise en place d\'antivirus, pare-feu et audits de sécurité.'],
            ['label_comp' => 'Développement web',          'description_comp' => 'Conception de sites web avec les technologies modernes.'],
            ['label_comp' => 'Support utilisateur',        'description_comp' => 'Assistance technique aux utilisateurs pour problèmes courants.'],
            ['label_comp' => 'Sauvegarde et récupération', 'description_comp' => 'Stratégies de sauvegarde et récupération de données.'],
            ['label_comp' => 'Administration serveur',     'description_comp' => 'Installation et maintenance de serveurs Linux et Windows Server.'],
            ['label_comp' => 'Câblage informatique',       'description_comp' => 'Pose et raccordement de câbles réseau RJ45 et fibre optique.'],
            ['label_comp' => 'Virtualisation',             'description_comp' => 'Mise en place de machines virtuelles avec VMware ou VirtualBox.'],
            ['label_comp' => 'Téléphonie IP',              'description_comp' => 'Installation de systèmes de téléphonie VoIP et IPBX.'],
            ['label_comp' => 'Maintenance écran',          'description_comp' => 'Réparation d\'écrans d\'ordinateurs portables et moniteurs.'],
            ['label_comp' => 'Récupération de données',    'description_comp' => 'Récupération de données perdues suite à une panne disque.'],
            ['label_comp' => 'Configuration VPN',          'description_comp' => 'Mise en place de tunnels VPN sécurisés pour l\'accès à distance.'],
            ['label_comp' => 'Gestion base de données',    'description_comp' => 'Administration et optimisation de bases de données MySQL/PostgreSQL.'],
        ];

        foreach ($competences as $competence) {
            Competence::create($competence);
        }

        // On complète avec des données générées par la factory pour avoir plus de variété
        Competence::factory(5)->create();
    }
}
