<?php

namespace Tests\Feature;

use App\Models\Competence;
use App\Models\User_Competence;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserCompetenceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Utilisateur
    {
        return Utilisateur::factory()->admin()->create();
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/user-competences  →  index()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_liste_toutes_les_associations_user_competence(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();
        $competences = Competence::factory(3)->create();

        foreach ($competences as $c) {
            User_Competence::create([
                'code_user' => $technicien->code_user,
                'code_comp' => $c->code_comp,
            ]);
        }

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/user-competences');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    // ═══════════════════════════════════════════════════════
    //  POST /api/user-competences  →  store()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_assigne_une_competence_a_un_utilisateur(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/user-competences', [
                             'code_user' => $technicien->code_user,
                             'code_comp' => $competence->code_comp,
                         ]);

        $response->assertStatus(201);
        // La table s'appelle user_competence (sans s) dans la migration
        $this->assertDatabaseHas('user_competence', [
            'code_user' => $technicien->code_user,
            'code_comp' => $competence->code_comp,
        ]);
    }

    #[Test]
    public function test_erreur_409_si_competence_deja_assignee(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();
        $competence = Competence::factory()->create();

        // Première assignation
        User_Competence::create([
            'code_user' => $technicien->code_user,
            'code_comp' => $competence->code_comp,
        ]);

        // Deuxième tentative → conflit
        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/user-competences', [
                             'code_user' => $technicien->code_user,
                             'code_comp' => $competence->code_comp,
                         ]);

        $response->assertStatus(409);
        $response->assertJsonFragment(['message' => 'Competence already assigned to user']);
    }

    #[Test]
    public function test_erreur_422_si_utilisateur_inexistant(): void
    {
        $admin      = $this->admin();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/user-competences', [
                             'code_user' => 'USR-FANTOME',
                             'code_comp' => $competence->code_comp,
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user']);
    }

    #[Test]
    public function test_erreur_422_si_competence_inexistante(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/user-competences', [
                             'code_user' => $technicien->code_user,
                             'code_comp' => 99999,
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_comp']);
    }

    #[Test]
    public function test_erreur_422_si_champs_manquants(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/user-competences', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user', 'code_comp']);
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/user-competences/user/{code_user}  →  showByUser()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_recupere_competences_dun_utilisateur(): void
    {
        $admin       = $this->admin();
        $technicien  = Utilisateur::factory()->technicien()->create();
        $competences = Competence::factory(4)->create();

        foreach ($competences as $c) {
            User_Competence::create([
                'code_user' => $technicien->code_user,
                'code_comp' => $c->code_comp,
            ]);
        }

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/user-competences/user/' . $technicien->code_user);

        $response->assertStatus(200);
        $response->assertJsonCount(4);
    }

    #[Test]
    public function test_erreur_404_si_utilisateur_sans_competences(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/user-competences/user/' . $technicien->code_user);

        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => 'No competences found for this user']);
    }

    // ═══════════════════════════════════════════════════════
    //  DELETE /api/user-competences  →  destroy()
    // ═══════════════════════════════════════════════════════

    public function destroy(Request $request)
{
    try {
        // Forcer la lecture du body JSON pour les requêtes DELETE
        $data = $request->json()->all();

        $code_user = $data['code_user'] ?? $request->input('code_user');
        $code_comp = $data['code_comp'] ?? $request->input('code_comp');

        if (!$code_user || !$code_comp) {
            return response()->json(['message' => 'code_user et code_comp requis'], 422);
        }

        $deleted = User_Competence::where('code_user', $code_user)
            ->where('code_comp', $code_comp)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'User competence not found'], 404);
        }

        return response()->json(null, 204);

    } catch (\Exception $e) {
        Log::error('DESTROY UC ERROR: ' . $e->getMessage());
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
    #[Test]
    public function test_un_technicien_peut_avoir_plusieurs_competences(): void
    {
        $admin      = $this->admin();
        $technicien = Utilisateur::factory()->technicien()->create();
        $competences = Competence::factory(5)->create();

        foreach ($competences as $c) {
            $this->actingAs($admin, 'sanctum')
                 ->postJson('/api/user-competences', [
                     'code_user' => $technicien->code_user,
                     'code_comp' => $c->code_comp,
                 ])->assertStatus(201);
        }

        $this->assertDatabaseCount('user_competence', 5);
    }

    #[Test]
    public function test_acces_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/user-competences');
        $response->assertStatus(401);
    }
}
