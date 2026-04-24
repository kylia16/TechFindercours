<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    /**
     * Crée des utilisateurs en base de données.
     *
     * On crée d'abord des utilisateurs fixes (admin, client test, technicien test)
     * puis on génère le reste avec la factory.
     */
    public function run(): void
    {
        // ─── 1. Utilisateurs fixes (pour les tests manuels) ───────────────────
        Utilisateur::create([
            'code_user'     => 'USR-ADMIN01',
            'nom_user'      => 'Admin',
            'prenom_user'   => 'TechFinder',
            'login_user'    => 'admin',
            'password_user' => bcrypt('admin1234'),
            'tel_user'      => '+237600000000',
            'sexe_user'     => 'M',
            'role_user'     => 'admin',
            'etat_user'     => 'actif',
        ]);

        Utilisateur::create([
            'code_user'     => 'USR-CLI001',
            'nom_user'      => 'Dupont',
            'prenom_user'   => 'Jean',
            'login_user'    => 'jean.dupont',
            'password_user' => bcrypt('password'),
            'tel_user'      => '+237655111222',
            'sexe_user'     => 'M',
            'role_user'     => 'client',
            'etat_user'     => 'actif',
        ]);

        Utilisateur::create([
            'code_user'     => 'USR-TECH01',
            'nom_user'      => 'Ngo Biyong',
            'prenom_user'   => 'Marie',
            'login_user'    => 'marie.tech',
            'password_user' => bcrypt('password'),
            'tel_user'      => '+237699333444',
            'sexe_user'     => 'F',
            'role_user'     => 'technicien',
            'etat_user'     => 'actif',
        ]);

        // ─── 2. Génération en masse avec la factory ───────────────────────────
        // 30 clients actifs
        Utilisateur::factory(30)->client()->create();

        // 20 techniciens actifs
        Utilisateur::factory(20)->technicien()->create();

        // 5 utilisateurs bloqués (pour tester les cas d'erreur)
        Utilisateur::factory(5)->bloque()->create();

        // 5 utilisateurs inactifs (en attente de validation)
        Utilisateur::factory(5)->inactif()->create();
    }
}
