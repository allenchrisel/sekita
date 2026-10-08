<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'email' => 'ahmad.provider@antari.id',
                'category' => 'guru-les-privat',
                'province_code' => '34',
                'regency_code' => '34.04',
                'district_code' => '34.04.06',
                'title' => 'Guru Privat Matematika & Fisika SMA',
                'bio' => 'Lulusan S.Pd. Pendidikan Matematika dengan pengalaman 7 tahun mengajar privat siswa SMP-SMA. Metode belajar santai, fokus pemahaman konsep dan persiapan UTBK.',
                'starting_price' => 75000,
                'whatsapp_number' => '081234567890',
                'instagram_url' => 'https://instagram.com/ahmad.mengajar',
                'linkedin_url' => 'https://linkedin.com/in/ahmad-subagja',
                'website_url' => null,
                'address' => 'Jl. Kaliurang Km 5, Sleman, Yogyakarta',
                'latitude' => -7.7686000,
                'longitude' => 110.3781000,
                'id_verified_badge' => true,  // KTP sudah VERIFIED (lihat VerificationDocumentSeeder)
                'degree_badge' => false,      // Ijazah masih PENDING -> bisa diuji dari panel Admin
            ],
            [
                'email' => 'siti.provider@antari.id',
                'category' => 'kebersihan-laundry',
                'province_code' => '34',
                'regency_code' => '34.04',
                'district_code' => '34.04.02',
                'title' => 'Jasa Bersih Rumah & Kos Harian',
                'bio' => 'Melayani pembersihan rumah, kos, dan ruko. Membawa peralatan sendiri, teliti dan tepat waktu.',
                'starting_price' => 120000,
                'whatsapp_number' => '081311112222',
                'instagram_url' => null,
                'linkedin_url' => null,
                'website_url' => null,
                'address' => 'Jl. Godean Km 3, Yogyakarta',
                'latitude' => -7.7803000,
                'longitude' => 110.3439000,
                'id_verified_badge' => true,
                'degree_badge' => false,
            ],
            [
                'email' => 'dedi.provider@antari.id',
                'category' => 'teknisi-listrik-ac',
                'province_code' => '34',
                'regency_code' => '34.04',
                'district_code' => '34.04.07',
                'title' => 'Teknisi AC & Instalasi Listrik Panggilan',
                'bio' => 'Cuci AC, isi freon, bongkar pasang, dan perbaikan instalasi listrik rumah. Garansi servis 14 hari.',
                'starting_price' => 85000,
                'whatsapp_number' => '081322223333',
                'instagram_url' => 'https://instagram.com/dedi.teknisi',
                'linkedin_url' => null,
                'website_url' => null,
                'address' => 'Jl. Affandi, Depok, Sleman',
                'latitude' => -7.7825000,
                'longitude' => 110.3948000,
                'id_verified_badge' => false,
                'degree_badge' => false,
            ],
        ];

        foreach ($profiles as $data) {
            $user = User::where('email', $data['email'])->firstOrFail();
            $category = Category::where('slug', $data['category'])->firstOrFail();
            $existingProfileId = ProviderProfile::query()
                ->where('user_id', $user->id)
                ->value('id');

            unset($data['email'], $data['category']);

            ProviderProfile::updateOrCreate(
                ['user_id' => $user->id],
                $data + [
                    'category_id' => $category->id,
                    'slug' => ProviderProfile::uniqueSlugForName($user->name, $existingProfileId),
                ],
            );
        }
    }
}
