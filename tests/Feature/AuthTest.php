<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_inscription_avec_donnees_valides(): void
    {
        $response = $this->postJson('/api/register', [
            'code_user'                  => 'USR-NEW001',
            'nom_user'                   => 'Kamga',
            'prenom_user'                => 'Paul',
            'login_user'                 => 'paul.kamga',
            'password_user'              => 'secret123',
            'password_user_confirmation' => 'secret123',
            'sexe_user'                  => 'M',
            'role_user'                  => 'client',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('utilisateur', ['code_user' => 'USR-NEW001']);
    }

    #[Test]
    public function test_inscription_echoue_si_login_deja_pris(): void
    {
        Utilisateur::factory()->create(['login_user' => 'login.existant']);

        $response = $this->postJson('/api/register', [
            'code_user'                  => 'USR-NEW002',
            'nom_user'                   => 'Test',
            'prenom_user'                => 'User',
            'login_user'                 => 'login.existant',
            'password_user'              => 'secret123',
            'password_user_confirmation' => 'secret123',
            'role_user'                  => 'client',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['login_user']);
    }

    #[Test]
    public function test_inscription_echoue_si_confirmation_mot_de_passe_incorrecte(): void
    {
        $response = $this->postJson('/api/register', [
            'code_user'                  => 'USR-NEW003',
            'nom_user'                   => 'Test',
            'prenom_user'                => 'User',
            'login_user'                 => 'test.user',
            'password_user'              => 'secret123',
            'password_user_confirmation' => 'different456',
            'role_user'                  => 'client',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password_user']);
    }

    #[Test]
    public function test_connexion_avec_identifiants_corrects(): void
    {
        Utilisateur::factory()->create([
            'login_user'    => 'jean.test',
            'password_user' => bcrypt('monpassword'),
            'role_user'     => 'client',
            'etat_user'     => 'actif',
        ]);

        $response = $this->postJson('/api/login', [
            'login_user'    => 'jean.test',
            'password_user' => 'monpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'utilisateur', 'message']);
    }

    #[Test]
    public function test_connexion_echoue_avec_mauvais_mot_de_passe(): void
    {
        Utilisateur::factory()->create([
            'login_user'    => 'jean.test',
            'password_user' => bcrypt('bonpassword'),
            'etat_user'     => 'actif',
        ]);

        $response = $this->postJson('/api/login', [
            'login_user'    => 'jean.test',
            'password_user' => 'mauvaispassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Identifiants incorrects.']);
    }

    #[Test]
    public function test_connexion_echoue_avec_login_inexistant(): void
    {
        $response = $this->postJson('/api/login', [
            'login_user'    => 'utilisateur.fantome',
            'password_user' => 'nimportequoi',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function test_connexion_echoue_si_compte_bloque(): void
    {
        Utilisateur::factory()->bloque()->create([
            'login_user'    => 'bloque.user',
            'password_user' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'login_user'    => 'bloque.user',
            'password_user' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function test_connexion_echoue_si_compte_inactif(): void
    {
        Utilisateur::factory()->inactif()->create([
            'login_user'    => 'inactif.user',
            'password_user' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'login_user'    => 'inactif.user',
            'password_user' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function test_acces_profil_avec_token_valide(): void
    {
        $utilisateur = Utilisateur::factory()->client()->create([
            'etat_user' => 'actif',
        ]);

        $response = $this->actingAs($utilisateur, 'sanctum')
                         ->getJson('/api/profil');

        $response->assertStatus(200);
        $response->assertJsonFragment(['code_user' => $utilisateur->code_user]);
    }

    #[Test]
    public function test_acces_profil_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/profil');
        $response->assertStatus(401);
    }

    #[Test]
    public function test_deconnexion_reussie(): void
    {
        $utilisateur = Utilisateur::factory()->client()->create([
            'etat_user' => 'actif',
        ]);

        $response = $this->actingAs($utilisateur, 'sanctum')
                         ->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Déconnexion réussie.']);
    }

    #[Test]
    public function test_route_protegee_inaccessible_sans_token(): void
    {
        $response = $this->getJson('/api/competences');
        $response->assertStatus(401);
    }
}
