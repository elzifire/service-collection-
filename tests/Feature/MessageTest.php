<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // User dummy
        $this->user = User::factory()->create();
    }

    public function test_gagal_akses_tanpa_token()
    {
        $response = $this->postJson('/api/messages', []);

        $response->assertStatus(401);
    }

    public function test_gagal_kirim_pesan_ke_receiver_tidak_diizinkan()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/messages', [
            'sender_id' => $this->user->id,
            'receiver_id' => 999, // invalid
            'message' => 'Halo'
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                    'status' => false,
                    'message' => 'Receiver tidak diperbolehkan.'
                 ]);
    }

    public function test_berhasil_kirim_pesan_receiver_valid()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/messages', [
            'sender_id' => $this->user->id,
            'receiver_id' => 100,
            'message' => 'Tes pesan'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'status' => true
                 ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->user->id,
            'receiver_id' => 100,
            'message' => 'Tes pesan'
        ]);
    }

    public function test_berhasil_ambil_chat_antara_user_dan_admin()
    {
        Sanctum::actingAs($this->user);

        // Insert dummy messages
        Message::create([
            'sender_id' => $this->user->id,
            'receiver_id' => 100,
            'message' => 'Hai admin'
        ]);

        Message::create([
            'sender_id' => 100,
            'receiver_id' => $this->user->id,
            'message' => 'Halo user'
        ]);

        $response = $this->getJson('/api/messages?user_id='.$this->user->id.'&friend_id=100');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'status',
                    'messages'
                 ])
                 ->assertJsonCount(2, 'messages');
    }
}
