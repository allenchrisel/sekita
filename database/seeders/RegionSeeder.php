<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $dataPath = database_path('data/indonesia-regions.json');

        if (! is_file($dataPath)) {
            throw new RuntimeException('Offline Indonesia regions dataset is missing.');
        }

        $regions = json_decode(file_get_contents($dataPath), true, flags: JSON_THROW_ON_ERROR);
        $now = now();

        foreach (array_chunk(array_map(fn (array $region): array => [
            'code' => $region['code'],
            'name' => $region['name'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $regions['provinces']), 500) as $chunk) {
            DB::table('provinces')->upsert($chunk, ['code'], ['name', 'updated_at']);
        }

        foreach (array_chunk(array_map(fn (array $region): array => [
            'code' => $region['code'],
            'province_code' => $region['province_code'],
            'name' => $region['name'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $regions['regencies']), 500) as $chunk) {
            DB::table('regencies')->upsert($chunk, ['code'], ['province_code', 'name', 'updated_at']);
        }

        foreach (array_chunk(array_map(fn (array $region): array => [
            'code' => $region['code'],
            'regency_code' => $region['regency_code'],
            'name' => $region['name'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $regions['districts']), 500) as $chunk) {
            DB::table('districts')->upsert($chunk, ['code'], ['regency_code', 'name', 'updated_at']);
        }
    }
}