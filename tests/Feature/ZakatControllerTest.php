<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ZakatControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function it_calculates_zakat_emas_correctly()
    {
        $response = $this->postJson('/api/hitung-emas', [
            'jumlahemas' => 100,
            'hargaemas_per_gram' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'hargaemas' => 100000,
                'total' => 2500,
            ]);
    }

    public function it_calculates_zakat_perak_correctly()
    {
        $response = $this->postJson('/api/hitung-perak', [
            'jumlahperak' => 100,
            'hargaperak_per_gram' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'hargaperak' => 100000,
                'total' => 2500,
            ]);
    }

    public function it_calculates_zakat_perdagangan_correctly()
    {
        $response = $this->postJson('/api/hitung-perdagangan', [
            'jumlahperdagangan' => 100,
            'hargaperdagangan' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'hargaperdagangan' => 100000,
                'total' => 2500,
            ]);
    }

    public function it_returns_error_when_jumlahemas_is_missing()
    {
        $response = $this->postJson('/api/hitung-emas', [
            'hargaemas_per_gram' => 1000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('jumlahemas');
    }

    public function it_returns_error_when_hargaemas_per_gram_is_missing()
    {
        $response = $this->postJson('/api/hitung-emas', [
            'jumlahemas' => 100,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('hargaemas_per_gram');
    }

    public function it_returns_error_when_jumlahperak_is_missing()
    {
        $response = $this->postJson('/api/hitung-perak', [
            'hargaperak_per_gram' => 1000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('jumlahperak');
    }
}
