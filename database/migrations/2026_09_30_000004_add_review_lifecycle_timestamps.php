<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('verification_documents', function (Blueprint $table): void {
            $table->timestamp('reviewed_at')->nullable();
        });

        Schema::table('review_disputes', function (Blueprint $table): void {
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('review_disputes', function (Blueprint $table): void {
            $table->dropColumn('resolved_at');
        });

        Schema::table('verification_documents', function (Blueprint $table): void {
            $table->dropColumn('reviewed_at');
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};