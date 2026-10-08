<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table): void {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table): void {
            $table->string('code', 10)->primary();
            $table->string('province_code', 10)->index();
            $table->string('name');
            $table->timestamps();
            $table->foreign('province_code')->references('code')->on('provinces')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('districts', function (Blueprint $table): void {
            $table->string('code', 10)->primary();
            $table->string('regency_code', 10)->index();
            $table->string('name');
            $table->timestamps();
            $table->foreign('regency_code')->references('code')->on('regencies')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};