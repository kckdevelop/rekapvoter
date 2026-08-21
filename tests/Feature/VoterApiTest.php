<?php

namespace Tests\Feature;

use App\Models\Tps;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoterApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tps $tps;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tps  = Tps::create(['nama_tps' => 'TPS 01']);
    }

    public function test_can_show_voter_via_api()
    {
        $voter = Voter::create(['tps_id' => $this->tps->id, 'nama' => 'Budi Santoso']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/voters/{$voter->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id'   => $voter->id,
                    'nama' => 'Budi Santoso',
                ],
            ]);
    }

    public function test_can_update_voter_via_put_and_post()
    {
        $voter = Voter::create(['tps_id' => $this->tps->id, 'nama' => 'Budi Lama']);

        // Test POST update (as requested by mobile client)
        $responsePost = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/voters/{$voter->id}", [
                'tps_id' => $this->tps->id,
                'nama'   => 'Budi Baru POST',
            ]);

        $responsePost->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama' => 'Budi Baru POST',
                ],
            ]);

        // Test PUT update (standard RESTful)
        $responsePut = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/voters/{$voter->id}", [
                'tps_id' => $this->tps->id,
                'nama'   => 'Budi Baru PUT',
            ]);

        $responsePut->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama' => 'Budi Baru PUT',
                ],
            ]);

        $this->assertDatabaseHas('voters', [
            'id'   => $voter->id,
            'nama' => 'Budi Baru PUT',
        ]);
    }

    public function test_can_delete_voter_via_api()
    {
        $voter = Voter::create(['tps_id' => $this->tps->id, 'nama' => 'Hapus Saya']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/voters/{$voter->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data pemilih berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('voters', ['id' => $voter->id]);
    }

    public function test_can_bulk_delete_voters_via_api()
    {
        $v1 = Voter::create(['tps_id' => $this->tps->id, 'nama' => 'V1']);
        $v2 = Voter::create(['tps_id' => $this->tps->id, 'nama' => 'V2']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/voters/bulk-delete", [
                'voter_ids' => [$v1->id, $v2->id],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'       => true,
                'deleted_count' => 2,
            ]);

        $this->assertDatabaseMissing('voters', ['id' => $v1->id]);
        $this->assertDatabaseMissing('voters', ['id' => $v2->id]);
    }
}
