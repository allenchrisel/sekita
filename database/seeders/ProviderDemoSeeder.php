<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\District;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProviderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->orderBy('id')->get();
        $districts = District::query()
            ->with('regency.province')
            ->orderBy('code')
            ->get();

        if ($categories->isEmpty() || $districts->isEmpty()) {
            throw new RuntimeException('Seed categories and Indonesia regions before demo providers.');
        }

        $givenNames = [
            'Aditya', 'Agung', 'Andini', 'Bayu', 'Citra', 'Dimas', 'Dewi', 'Fajar',
            'Fitri', 'Galih', 'Intan', 'Laras', 'Maya', 'Nadia', 'Putra', 'Raka',
            'Rani', 'Rizky', 'Salsa', 'Teguh',
        ];
        $familyNames = [
            'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Permata', 'Nugraha', 'Lestari',
            'Mahendra', 'Puspita', 'Ramadhan', 'Wibowo', 'Ananda', 'Setiawan', 'Utami',
            'Firmansyah', 'Hidayat', 'Santoso', 'Anggraini', 'Maulana', 'Siregar',
        ];

        foreach (range(1, 100) as $number) {
            $index = $number - 1;
            $category = $categories[$index % $categories->count()];
            $district = $districts[($index * 73) % $districts->count()];
            $regency = $district->regency;
            $province = $regency->province;
            $email = sprintf('provider.demo.%03d@sekita.id', $number);
            $phone = sprintf('0815%08d', $number);
            $name = $givenNames[$index % count($givenNames)].' '.$familyNames[intdiv($index, count($givenNames)) % count($familyNames)];

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'phone' => $phone,
                    'password' => 'password123',
                    'role' => UserRole::PROVIDER,
                    'is_verified' => false,
                    'email_verified_at' => now(),
                ],
            );

            $existingProfileId = ProviderProfile::query()
                ->where('user_id', $user->id)
                ->value('id');

            ProviderProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => ProviderProfile::uniqueSlugForName($name, $existingProfileId),
                    'category_id' => $category->id,
                    'province_code' => $province->code,
                    'regency_code' => $regency->code,
                    'district_code' => $district->code,
                    'title' => $category->name.' · '.$name,
                    'bio' => "Penyedia jasa {$category->name} lokal di {$district->name}, {$regency->name}. Melayani kebutuhan rumah dan usaha dengan komunikasi yang jelas dan jadwal fleksibel.",
                    'starting_price' => 50000 + (($index % 10) * 15000),
                    'whatsapp_number' => $phone,
                    'address' => 'Area '.$district->name.', '.$regency->name,
                    'id_verified_badge' => false,
                    'degree_badge' => false,
                ],
            );
        }
    }
}
