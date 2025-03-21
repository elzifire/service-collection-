<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image');
            $table->decimal('goal_amount', 15, 2);
            $table->decimal('total_collected', 15, 2)->default(0);
            $table->text('description');
            $table->date('expired');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories_campaign');
            $table->string('bank_info');
            $table->string('status');
            $table->string('file_qr');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};