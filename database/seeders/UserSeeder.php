<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Akun utama untuk pengujian Role-Based Access
            ['Admin Antari', 'admin@antari.id', '081200000001', UserRole::ADMIN, true],
            ['Ahmad Subagja, S.Pd.', 'ahmad.provider@antari.id', '081234567890', UserRole::PROVIDER, true],
            ['Budi Santoso', 'budi.client@antari.id', '081298765432', UserRole::CLIENT, true],

            // Akun tambahan agar daftar & ulasan terlihat realistis
            ['Siti Rahmawati', 'siti.provider@antari.id', '081311112222', UserRole::PROVIDER, true],
            ['Dedi Kurniawan', 'dedi.provider@antari.id', '081322223333', UserRole::PROVIDER, true],
            ['Rina Wulandari', 'rina.client@antari.id', '081333334444', UserRole::CLIENT, true],
            ['Agus Prasetyo', 'agus.client@antari.id', '081344445555', UserRole::CLIENT, true],
        ];

        foreach ($users as [$name, $email, $phone, $role, $verified]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'phone' => $phone,
                    'password' => 'password123', // di-hash otomatis oleh cast 'hashed'
                    'role' => $role,
                    'is_verified' => $verified,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
