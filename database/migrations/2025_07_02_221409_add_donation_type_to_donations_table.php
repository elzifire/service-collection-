<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('donasi')->table('donations', function (Blueprint $table) {
            $table->enum('donation_type', ['umum', 'terdaftar'])->default('umum')->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::connection('donasi')->table('donations', function (Blueprint $table) {
            $table->dropColumn('donation_type');
        });
    }
};