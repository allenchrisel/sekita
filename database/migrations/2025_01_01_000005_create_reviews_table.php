<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5 (divalidasi di FormRequest)
            $table->string('comment', 500);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            // Mendukung pengecekan rate limit: 1 ulasan / provider / 30 hari
            $table->index(['provider_profile_id', 'client_id', 'created_at']);
            $table->index(['provider_profile_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
