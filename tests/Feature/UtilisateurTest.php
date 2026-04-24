<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UtilisateurTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Utilisateur
    {
        return Utilisateur::factory()->admin()->create();
    }


    #[Test]
    public function test_liste_tous_les_utilisateurs(): void
    {
        $admin = $this->admin();
        Utilisateur::factory(4)->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/utilisateurs');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    #[Test]
    public function test_liste_vide_quand_seul_admin(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/utilisateurs');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }


    #[Test]
    public function test_cree_un_utilisateur_valide(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/utilisateurs', [
                             'code_user'     => 'USR-TEST01',
                             'nom_user'      => 'Kamga',
                             'prenom_user'   => 'Paul',
                             'login_user'    => 'paul.kamga',
                             'password_user' => 'secret123',
                             'tel_user'      => '+237699001122',
                             'sexe_user'     => 'M',
                             'role_user'     => 'client',
                             'etat_user'     => true,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('utilisateur', ['code_user' => 'USR-TEST01']);
    }

    #[Test]
    public function test_erreur_si_code_user_duplique(): void
    {
        $admin = $this->admin();
        Utilisateur::factory()->create(['code_user' => 'USR-DUPLI1']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/utilisateurs', [
                             'code_user'     => 'USR-DUPLI1',
                             'nom_user'      => 'Test',
                             'prenom_user'   => 'User',
                             'login_user'    => 'login.unique99',
                             'password_user' => 'password123',
                             'role_user'     => 'client',
                             'etat_user'     => true,
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user']);
    }

    #[Test]
    public function test_erreur_si_login_duplique(): void
    {
        $admin = $this->admin();
        Utilisateur::factory()->create(['login_user' => 'login.existant']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/utilisateurs', [
                             'code_user'     => 'USR-NEW001',
                             'nom_user'      => 'Test',
                             'prenom_user'   => 'User',
                             'login_user'    => 'login.existant',
                             'password_user' => 'password123',
                             'role_user'     => 'client',
                             'etat_user'     => true,
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['login_user']);
    }

    #[Test]
    public function test_erreur_si_sexe_invalide(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/utilisateurs', [
                             'code_user'     => 'USR-SEX001',
                             'nom_user'      => 'Test',
                             'prenom_user'   => 'User',
                             'login_user'    => 'test.sexe',
                             'password_user' => 'password123',
                             'sexe_user'     => 'X', // invalide
                             'role_user'     => 'client',
                             'etat_user'     => true,
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sexe_user']);
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/utilisateurs/{code}  →  show()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_affiche_un_utilisateur_existant(): void
    {
        $admin = $this->admin();
        $utilisateur = Utilisateur::factory()->create(['nom_user' => 'Mbarga']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/utilisateurs/' . $utilisateur->code_user);

        $response->assertStatus(200);
        $response->assertJsonFragment(['nom_user' => 'Mbarga']);
    }

    #[Test]
    public function test_erreur_404_si_utilisateur_inexistant(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/utilisateurs/USR-INEXISTANT');

        $response->assertStatus(404);
    }

    // ═══════════════════════════════════════════════════════
    //  DELETE /api/utilisateurs/{code}  →  destroy()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_supprime_un_utilisateur(): void
    {
        $admin = $this->admin();
        $utilisateur = Utilisateur::factory()->create();
        $code = $utilisateur->code_user;

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson('/api/utilisateurs/' . $code);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('utilisateur', ['code_user' => $code]);
    }

    // ═══════════════════════════════════════════════════════
    //  Structure JSON
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_structure_json_utilisateur(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/utilisateurs/' . $admin->code_user);

        $response->assertJsonStructure([
            'code_user', 'nom_user', 'prenom_user',
            'login_user', 'tel_user', 'sexe_user',
            'role_user', 'etat_user', 'created_at', 'updated_at',
        ]);
    }

    #[Test]
    public function test_acces_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/utilisateurs');
        $response->assertStatus(401);
    }
}
