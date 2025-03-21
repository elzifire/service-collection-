<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;
use App\Models\User;
use App\Models\CategoriesCampaigns;
use Illuminate\Support\Str;
class CampaignFactory extends Factory
{
    
    
    protected $model = Campaign::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $slug = Str::slug($title);
        $goalAmount = $this->faker->randomFloat(2, 500000, 50000000); // 500k - 50jt
        $totalCollected = $this->faker->randomFloat(2, 0, $goalAmount);
        $statuses = ['acc', 'pending', 'ditolak']; // 3 status yang diinginkan
        return [
            'title' => $title,
            'slug' => $slug,
            // 'image' => 'campaigns/' . $this->faker->image(storage_path('app/public/campaigns'), 640, 480, null, false),
            // fake file image
            'image' => 'campaigns/' . $this->faker->uuid . '.png', 
            'goal_amount' => $goalAmount,
            'total_collected' => $totalCollected,
            'description' => $this->faker->paragraph(5),
            'expired' => $this->faker->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'category_id' => CategoriesCampaigns::factory(), // Factory kategori campaign
            'bank_info' => 'BCA - 1234567890 a/n Yayasan Donasi',
            'status' => $this->faker->randomElement($statuses),
            'file_qr' => 'qrcodes/' . $this->faker->uuid . '.png',
        ];
    }
}
