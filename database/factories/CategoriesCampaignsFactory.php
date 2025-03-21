<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CategoriesCampaigns; // Tambahkan model

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriesCampaigns>
 */
class CategoriesCampaignsFactory extends Factory
{
    protected $model = CategoriesCampaigns::class; // Tambahkan ini

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
