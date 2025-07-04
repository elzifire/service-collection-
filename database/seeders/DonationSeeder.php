<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Donation;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Status (Acc, Pending, Ditolak) di koneksi donasi
        $statuses = ['Pending', 'Acc', 'Ditolak'];
        foreach ($statuses as $status) {
            Status::on('donasi')->firstOrCreate(['name' => $status]);
        }

        // 2. Seed Users (Membuat 5 user) di koneksi mysql
        User::factory(5)->create();

        // 3. Seed Campaigns (Membuat 3 campaign) di koneksi donasi
        Campaign::factory()->count(3)->connection('donasi')->create();

        // 4. Seed Donations (Membuat 10 donasi) di koneksi donasi
        Donation::factory()->count(10)->connection('donasi')->create();
    }
}