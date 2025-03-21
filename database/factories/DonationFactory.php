<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Buat user baru jika tidak ada
            'campaign_id' => Campaign::factory(), // Buat campaign baru jika tidak ada
            'status_id' => Status::inRandomOrder()->first()?->id ?? 1, // Pilih status acak atau default ke pending
            'amount' => fake()->randomFloat(2, 10000, 1000000), // Donasi antara 10 ribu - 1 juta
            'proof_image' => fake()->imageUrl(), // Gambar bukti pembayaran
        ];
    }
}
