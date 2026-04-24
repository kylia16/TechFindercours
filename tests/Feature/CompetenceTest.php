<?php

namespace Tests\Feature;

use App\Models\Competence;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class CompetenceTest extends TestCase
{
    use RefreshDatabase;

    private function utilisateurConnecte(): Utilisateur
    {
        return Utilisateur::factory()->admin()->create();
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/competences  →  index()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_liste_toutes_les_competences(): void
    {
        $user = $this->utilisateurConnecte();
        Competence::factory(3)->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/competences');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    #[Test]
    public function test_liste_vide_quand_aucune_competence(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/competences');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    // ═══════════════════════════════════════════════════════
    //  POST /api/competences  →  store()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_cree_une_competence_avec_donnees_valides(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/competences', [
                             'label_comp'       => 'Réparation PC',
                             'description_comp' => 'Diagnostic et réparation de pannes.',
                         ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['label_comp' => 'Réparation PC']);
        $this->assertDatabaseHas('competences', ['label_comp' => 'Réparation PC']);
    }

    #[Test]
    public function test_cree_une_competence_sans_description(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/competences', [
                             'label_comp' => 'Support réseau',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('competences', [
            'label_comp'       => 'Support réseau',
            'description_comp' => null,
        ]);
    }

    #[Test]
    public function test_erreur_validation_si_label_manquant(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/competences', [
                             'description_comp' => 'Description sans label',
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label_comp']);
    }

    #[Test]
    public function test_erreur_validation_si_label_trop_long(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/competences', [
                             'label_comp' => str_repeat('A', 256),
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['label_comp']);
    }

    // ═══════════════════════════════════════════════════════
    //  GET /api/competences/{id}  →  show()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_affiche_une_competence_existante(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create(['label_comp' => 'Sécurité réseau']);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/competences/' . $competence->code_comp);

        $response->assertStatus(200);
        $response->assertJsonFragment(['label_comp' => 'Sécurité réseau']);
    }

    #[Test]
    public function test_erreur_si_competence_inexistante(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/competences/99999');

        $this->assertContains($response->status(), [404, 500]);
    }

    // ═══════════════════════════════════════════════════════
    //  PUT /api/competences/{id}  →  update()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_met_a_jour_une_competence(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create(['label_comp' => 'Ancien label']);

        $response = $this->actingAs($user, 'sanctum')
                         ->putJson('/api/competences/' . $competence->code_comp, [
                             'label_comp'       => 'Nouveau label',
                             'description_comp' => 'Nouvelle description.',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('competences', ['label_comp' => 'Nouveau label']);
        $this->assertDatabaseMissing('competences', ['label_comp' => 'Ancien label']);
    }

    #[Test]
    public function test_mise_a_jour_partielle_possible(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->putJson('/api/competences/' . $competence->code_comp, [
                             'description_comp' => 'Description mise à jour',
                         ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function test_erreur_validation_update_si_label_trop_long(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->putJson('/api/competences/' . $competence->code_comp, [
                             'label_comp' => str_repeat('X', 300),
                         ]);

        $response->assertStatus(422);
    }

    // ═══════════════════════════════════════════════════════
    //  DELETE /api/competences/{id}  →  destroy()
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_supprime_une_competence_existante(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/competences/' . $competence->code_comp);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('competences', ['code_comp' => $competence->code_comp]);
    }

    #[Test]
    public function test_erreur_si_suppression_competence_inexistante(): void
    {
        $user = $this->utilisateurConnecte();

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/competences/99999');

        $this->assertContains($response->status(), [404, 500]);
    }

    // ═══════════════════════════════════════════════════════
    //  Structure JSON
    // ═══════════════════════════════════════════════════════

    #[Test]
    public function test_structure_json_retournee(): void
    {
        $user = $this->utilisateurConnecte();
        $competence = Competence::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/competences/' . $competence->code_comp);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code_comp', 'label_comp', 'description_comp',
            'created_at', 'updated_at',
        ]);
    }

    #[Test]
    public function test_acces_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/competences');
        $response->assertStatus(401);

    }
    //tester la modificication d'une competence
    #[Test]
        public function test_modification_competence(): void
        {
            $user = $this->utilisateurConnecte();
            $competence = Competence::factory()->create();
            $data = ['label_comp' => 'Nouveau label test'];

            $response = $this->actingAs($user, 'sanctum')
                            ->putJson('/api/competences/' . $competence->code_comp, $data);

            $response->assertStatus(200);
        }
}
