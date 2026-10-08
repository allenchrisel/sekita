<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table): void {
            $table->string('province_code', 10)->nullable()->after('category_id');
            $table->string('regency_code', 10)->nullable()->after('province_code');
            $table->string('district_code', 10)->nullable()->after('regency_code');
            $table->foreign('province_code')->references('code')->on('provinces')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('regency_code')->references('code')->on('regencies')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('district_code')->references('code')->on('districts')->nullOnDelete()->cascadeOnUpdate();
            $table->dropColumn('service_radius_km');
        });
    }

    public function down(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table): void {
            $table->dropForeign(['district_code']);
            $table->dropForeign(['regency_code']);
            $table->dropForeign(['province_code']);
            $table->dropColumn(['district_code', 'regency_code', 'province_code']);
            $table->unsignedSmallInteger('service_radius_km')->default(10);
        });
    }
};