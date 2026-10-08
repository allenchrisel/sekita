<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoProviderReviewSeeder extends Seeder
{
    private const COMMENTS = [
        'Contoh ulasan demo: komunikasi jelas dan hasil pekerjaan sesuai kesepakatan.',
        'Contoh ulasan demo: pelayanan ramah, tepat waktu, dan prosesnya lancar.',
        'Contoh ulasan demo: penjelasannya mudah dipahami dan pekerjaan dilakukan dengan rapi.',
        'Contoh ulasan demo: respons cepat dan layanan sesuai kebutuhan saya.',
        'Contoh ulasan demo: pengalaman menggunakan jasa ini baik dan informatif.',
    ];

    public function run(): void
    {
        $client = User::query()
            ->where('email', 'budi.client@antari.id')
            ->where('role', UserRole::CLIENT)
            ->where('is_verified', true)
            ->first();

        if ($client === null) {
            throw new RuntimeException('Seed the verified demo client before demo provider reviews.');
        }

        $profiles = ProviderProfile::query()
            ->whereHas('user', fn ($query) => $query->where('email', 'like', 'provider.demo.%@sekita.id'))
            ->with('user')
            ->orderBy('id')
            ->get();

        if ($profiles->count() !== 100) {
            throw new RuntimeException("Expected 100 demo provider profiles, found {$profiles->count()}.");
        }

        foreach ($profiles as $index => $profile) {
            Review::updateOrCreate(
                [
                    'provider_profile_id' => $profile->id,
                    'client_id' => $client->id,
                ],
                [
                    'rating' => match ($index % 5) {
                        0 => 4,
                        default => 5,
                    },
                    'comment' => self::COMMENTS[$index % count(self::COMMENTS)],
                    'is_published' => true,
                ],
            );

            $profile->refreshRatingStats();
        }
    }
}
