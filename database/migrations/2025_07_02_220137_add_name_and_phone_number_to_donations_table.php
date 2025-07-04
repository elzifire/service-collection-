<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('donasi')->table('donations', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('phone_number')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::connection('donasi')->table('donations', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone_number']);
        });
    }
};