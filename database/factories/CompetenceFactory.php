<?php

namespace Database\Factories;

use App\Models\Competence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competence>
 */
class CompetenceFactory extends Factory
{
    protected $model = Competence::class;

    // Liste de compétences techniques réalistes (domaine IT/TechFinder)
    private array $competencesIT = [
        ['label' => 'Réparation PC',         'desc' => 'Diagnostic et réparation de pannes matérielles et logicielles sur ordinateurs fixes et portables.'],
        ['label' => 'Installation réseau',   'desc' => 'Mise en place et configuration de réseaux filaires et Wi-Fi (routeurs, switches, points d\'accès).'],
        ['label' => 'Maintenance imprimante','desc' => 'Entretien, dépannage et remplacement de consommables pour imprimantes laser et jet d\'encre.'],
        ['label' => 'Sécurité informatique', 'desc' => 'Mise en place d\'antivirus, pare-feu et audits de sécurité pour protéger les systèmes d\'information.'],
        ['label' => 'Développement web',      'desc' => 'Conception et développement de sites et applications web avec les technologies modernes.'],
        ['label' => 'Support utilisateur',   'desc' => 'Assistance technique aux utilisateurs pour la résolution de problèmes courants informatiques.'],
        ['label' => 'Sauvegarde et récupération', 'desc' => 'Mise en place de stratégies de sauvegarde et récupération de données après sinistre.'],
        ['label' => 'Administration serveur','desc' => 'Installation, configuration et maintenance de serveurs Linux et Windows Server.'],
        ['label' => 'Câblage informatique',  'desc' => 'Pose, raccordement et certification de câbles réseau RJ45 et fibre optique.'],
        ['label' => 'Virtualisation',        'desc' => 'Mise en place de machines virtuelles avec VMware, VirtualBox ou Hyper-V.'],
        ['label' => 'Téléphonie IP',         'desc' => 'Installation et configuration de systèmes de téléphonie sur IP (VoIP, IPBX).'],
        ['label' => 'Maintenance écran',     'desc' => 'Réparation et remplacement d\'écrans d\'ordinateurs portables, moniteurs et tablettes.'],
        ['label' => 'Récupération de données','desc' => 'Récupération de données perdues suite à une panne disque, suppression accidentelle ou formatage.'],
        ['label' => 'Configuration VPN',     'desc' => 'Mise en place de tunnels VPN sécurisés pour l\'accès à distance aux ressources d\'entreprise.'],
        ['label' => 'Gestion de bases de données', 'desc' => 'Administration et optimisation de bases de données MySQL, PostgreSQL et SQL Server.'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // On pioche aléatoirement dans notre liste réaliste
        $competence = $this->faker->randomElement($this->competencesIT);

        return [
            // label_comp : on ajoute un numéro aléatoire pour éviter les doublons stricts
            'label_comp'       => $competence['label'] . ' ' . $this->faker->numberBetween(1, 999),
            'description_comp' => $competence['desc'],
        ];
    }

    /**
     * State : compétence avec un label spécifique (utile dans les tests)
     */
    public function avecLabel(string $label): static
    {
        return $this->state(fn (array $attributes) => [
            'label_comp' => $label,
        ]);
    }

    /**
     * State : compétence sans description (champ nullable)
     */
    public function sansDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description_comp' => null,
        ]);
    }
}
