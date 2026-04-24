<?php

namespace Tests\Feature;

use App\Models\Competence;
use App\Models\Intervention;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InterventionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Utilisateur
    {
        return Utilisateur::factory()->admin()->create();
    }

    private function creerClient(): Utilisateur
    {
        return Utilisateur::factory()->client()->create();
    }

    private function creerTechnicien(): Utilisateur
    {
        return Utilisateur::factory()->technicien()->create();
    }

    private function creerCompetence(): Competence
    {
        return Competence::factory()->create();
    }

    private function donneeValide(): array
    {
        return [
            'note_int'         => 15,
            'commentaire_int'  => 'Intervention rapide et efficace.',
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/interventions  →  index()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_liste_toutes_les_interventions(): void
    {
        $admin      = $this->admin();
        $client     = $this->creerClient();
        $technicien = $this->creerTechnicien();
        $competence = $this->creerCompetence();

        Intervention::factory(3)->create([
            'code_user_client' => $client->code_user,
            'code_user_techn'  => $technicien->code_user,
            'code_comp'        => $competence->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/interventions');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    #[Test]
    public function test_liste_vide_quand_aucune_intervention(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/interventions');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    // ═══════════════════════════════════════════════════════
    //  POST /api/interventions  →  store()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_cree_une_intervention_valide(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $this->donneeValide());

        $response->assertStatus(201);
        $this->assertDatabaseHas('intervention', ['note_int' => 15]);
    }

    #[Test]
    public function test_date_auto_definie_a_la_creation(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $this->donneeValide());

        $response->assertStatus(201);
        $this->assertNotNull($response->json('date_int'));
    }

    #[Test]
    public function test_creation_sans_commentaire_possible(): void
    {
        $admin = $this->admin();
        $data  = $this->donneeValide();
        unset($data['commentaire_int']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $data);

        $response->assertStatus(201);
    }

    #[Test]
    public function test_erreur_si_note_manquante(): void
    {
        $admin = $this->admin();
        $data  = $this->donneeValide();
        unset($data['note_int']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['note_int']);
    }

    #[Test]
    public function test_erreur_si_note_hors_limites(): void
    {
        $admin        = $this->admin();
        $data         = $this->donneeValide();
        $data['note_int'] = 25; // max = 20

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['note_int']);
    }

    #[Test]
    public function test_erreur_si_note_negative(): void
    {
        $admin            = $this->admin();
        $data             = $this->donneeValide();
        $data['note_int'] = -1;

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['note_int']);
    }

    #[Test]
    public function test_erreur_si_client_manquant(): void
    {
        $admin = $this->admin();
        $data  = $this->donneeValide();
        unset($data['code_user_client']);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/interventions', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code_user_client']);
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/interventions/{id}  →  show()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_affiche_une_intervention_par_id(): void
    {
        $admin        = $this->admin();
        $intervention = Intervention::create([
            'date_int'         => now(),
            'note_int'         => 17,
            'commentaire_int'  => 'Très bon travail.',
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/interventions/' . $intervention->code_int);

        $response->assertStatus(200);
        $response->assertJsonFragment(['note_int' => 17]);
    }

    #[Test]
    public function test_erreur_si_intervention_inexistante(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/interventions/99999');

        $this->assertContains($response->status(), [404, 500]);
    }

    // ═══════════════════════════════════════════════════════
    //  PUT /api/interventions/{id}  →  update()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_met_a_jour_une_intervention(): void
    {
        $admin        = $this->admin();
        $intervention = Intervention::create([
            'date_int'         => now(),
            'note_int'         => 10,
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson('/api/interventions/' . $intervention->code_int, [
                             'note_int'        => 19,
                             'commentaire_int' => 'Commentaire mis à jour.',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('intervention', ['note_int' => 19]);
    }

    #[Test]
    public function test_erreur_validation_update_note_invalide(): void
    {
        $admin        = $this->admin();
        $intervention = Intervention::create([
            'date_int'         => now(),
            'note_int'         => 10,
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson('/api/interventions/' . $intervention->code_int, [
                             'note_int' => 50,
                         ]);

        $response->assertStatus(422);
    }

    // ═══════════════════════════════════════════════════════
    //  DELETE /api/interventions/{id}  →  destroy()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_supprime_une_intervention(): void
    {
        $admin        = $this->admin();
        $intervention = Intervention::create([
            'date_int'         => now(),
            'note_int'         => 12,
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson('/api/interventions/' . $intervention->code_int);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('intervention', ['code_int' => $intervention->code_int]);
    }

    // ═══════════════════════════════════════════════════════
    //  Structure JSON
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_structure_json_intervention(): void
    {
        $admin        = $this->admin();
        $intervention = Intervention::create([
            'date_int'         => now(),
            'note_int'         => 14,
            'code_user_client' => $this->creerClient()->code_user,
            'code_user_techn'  => $this->creerTechnicien()->code_user,
            'code_comp'        => $this->creerCompetence()->code_comp,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/interventions/' . $intervention->code_int);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code_int', 'date_int', 'note_int', 'commentaire_int',
            'code_user_client', 'code_user_techn', 'code_comp',
            'created_at', 'updated_at',
        ]);
    }

    #[Test]
    public function test_acces_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/interventions');
        $response->assertStatus(401);
    }
}
