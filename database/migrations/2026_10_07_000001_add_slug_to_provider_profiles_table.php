<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique();
        });

        DB::table('provider_profiles')
            ->join('users', 'provider_profiles.user_id', '=', 'users.id')
            ->select('provider_profiles.id', 'users.name')
            ->orderBy('provider_profiles.id')
            ->get()
            ->each(function (object $profile): void {
                $baseSlug = Str::slug($profile->name) ?: 'provider';
                $slug = $baseSlug;
                $suffix = 2;

                while (DB::table('provider_profiles')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$suffix++;
                }

                DB::table('provider_profiles')
                    ->where('id', $profile->id)
                    ->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
