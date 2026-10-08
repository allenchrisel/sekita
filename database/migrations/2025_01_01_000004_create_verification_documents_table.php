<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 20); // KTP, IJAZAH, SERTIFIKAT
            $table->string('private_file_url');  // path relatif di disk privat, BUKAN URL publik
            $table->string('status', 20)->default('PENDING'); // PENDING, VERIFIED, REJECTED
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'document_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
