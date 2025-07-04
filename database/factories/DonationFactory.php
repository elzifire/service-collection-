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
        $donationType = fake()->randomElement(['umum', 'terdaftar']);

        return [
            'user_id' => $donationType === 'terdaftar' ? User::inRandomOrder()->first()?->id : null,
            'campaign_id' => Campaign::inRandomOrder()->first()?->id ?? 1,
            'status_id' => Status::inRandomOrder()->first()?->id ?? 1,
            'amount' => fake()->randomFloat(2, 10000, 1000000),
            'proof_image' => fake()->imageUrl(),
            'name' => $donationType === 'umum' ? fake()->name() : null,
            'phone_number' => $donationType === 'umum' ? fake()->phoneNumber() : null,
            'donation_type' => $donationType,
        ];
    }
}