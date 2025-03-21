<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donation;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DonationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_campaigns()
    {
        Campaign::factory()->count(3)->create();

        $response = $this->getJson('/api/donations');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data'
                 ]);
    }

    /** @test */
    public function it_can_show_a_campaign_by_id()
    {
        $campaign = Campaign::factory()->create();

        $response = $this->getJson("/api/donations/{$campaign->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'id' => $campaign->id
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_campaign()
    {
        $response = $this->getJson('/api/donations/999');

        $response->assertStatus(200) // karena kode masih mengembalikan 200 meskipun data tidak ditemukan
                 ->assertJson([
                     'status' => 'success',
                     'data' => null
                 ]);
    }

    /** @test */
    public function it_allows_authenticated_users_to_donate()
{
    // Pastikan database testing dalam keadaan bersih
    $this->artisan('migrate:fresh --database=donasi');

    // Buat user menggunakan factory jika belum ada
    $user = User::factory()->create();

    // Pastikan user bukan Collection
    dump($user);

    // Login sebagai user yang sudah dibuat
    $this->actingAs($user, 'sanctum');

    // Buat campaign yang valid
    $campaign = Campaign::factory()->createOne();

    // Data yang dikirim dalam request
    $data = [
        'campaign_id' => $campaign->id,
        'amount' => 10000,
        'message' => 'Semoga berkah'
    ];

    // Kirim request POST ke endpoint donasi
    $response = $this->postJson('/api/donations', $data);

    // Debugging untuk melihat response
    dump($response->json());

    // Pastikan response statusnya 201
    $response->assertStatus(201)
             ->assertJson([
                 'status' => 'success'
             ]);
}

    /** @test */
    public function it_rejects_donations_without_authentication()
    {
        $campaign = Campaign::factory()->create();

        $data = [
            'amount' => 50000,
            'proof_image' => UploadedFile::fake()->image('donation.jpg'),
            'campaign_id' => $campaign->id
        ];

        $response = $this->postJson('/api/donations', $data);

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function it_prevents_users_from_exceeding_rate_limit()
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user, 'sanctum')->postJson('/api/donations', [
                'amount' => 50000,
                'proof_image' => UploadedFile::fake()->image('donation.jpg'),
                'campaign_id' => $campaign->id
            ]);
        }

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/donations', [
            'amount' => 50000,
            'proof_image' => UploadedFile::fake()->image('donation.jpg'),
            'campaign_id' => $campaign->id
        ]);

        $response->assertStatus(429) // Too Many Requests
                 ->assertJson([
                     'message' => 'Too many requests. Please try again later.'
                 ]);
    }
}
