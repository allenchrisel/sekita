<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->unique()->constrained()->cascadeOnDelete(); // 1 balasan per ulasan
            $table->foreignId('provider_profile_id')->constrained()->cascadeOnDelete();
            $table->string('reply_text', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};
