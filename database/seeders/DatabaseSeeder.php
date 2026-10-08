<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ProviderProfileSeeder::class,
            ProviderDemoSeeder::class,
            VerificationDocumentSeeder::class,
            ReviewSeeder::class,
            DemoProviderReviewSeeder::class,
        ]);
    }
}
