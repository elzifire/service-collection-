<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['user_id']);
            // Ubah user_id jadi nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            // Tambah foreign key lagi dengan nullOnDelete
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['user_id']);
            // Kembalikan user_id jadi non-nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            // Tambah foreign key tanpa nullOnDelete
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};