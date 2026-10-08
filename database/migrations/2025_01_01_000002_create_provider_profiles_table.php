<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('bio')->nullable();
            $table->unsignedInteger('starting_price')->nullable(); // Rupiah
            $table->string('whatsapp_number', 20);
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('service_radius_km')->default(10);
            $table->boolean('id_verified_badge')->default(false);
            $table->boolean('degree_badge')->default(false);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
