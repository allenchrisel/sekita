<?php

namespace Database\Seeders;

use App\Enums\DisputeStatus;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\ReviewDispute;
use App\Models\ReviewReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $ahmad = User::where('email', 'ahmad.provider@antari.id')->firstOrFail()->providerProfile;
        $siti = User::where('email', 'siti.provider@antari.id')->firstOrFail()->providerProfile;

        $budi = User::where('email', 'budi.client@antari.id')->firstOrFail();
        $rina = User::where('email', 'rina.client@antari.id')->firstOrFail();
        $agus = User::where('email', 'agus.client@antari.id')->firstOrFail();

        // Ulasan untuk Ahmad
        $budiReview = $this->review($ahmad, $budi, 5, 'Penjelasannya sabar dan mudah dipahami. Nilai Matematika anak saya naik dalam dua bulan.');
        $this->review($ahmad, $rina, 4, 'Guru tepat waktu dan materinya terstruktur. Semoga bisa tambah sesi latihan soal.');
        $spamReview = $this->review($ahmad, $agus, 1, 'Kurang cocok dengan jadwal yang tersedia, jadi belum bisa lanjut.');

        // Ulasan untuk Siti
        $this->review($siti, $budi, 5, 'Rumah jadi bersih dan rapi, pekerjanya ramah.');

        // Balasan publik provider
        ReviewReply::updateOrCreate(
            ['review_id' => $budiReview->id],
            [
                'provider_profile_id' => $ahmad->id,
                'reply_text' => 'Terima kasih Pak Budi, senang bisa membantu. Sampai jumpa di sesi berikutnya!',
            ],
        );

        // Dispute contoh agar Admin punya antrean moderasi untuk diuji
        ReviewDispute::updateOrCreate(
            ['review_id' => $spamReview->id, 'reporter_id' => $ahmad->user_id],
            [
                'reason' => 'Ulasan tidak sesuai kenyataan',
                'evidence_details' => 'Pengguna ini tidak pernah mengikuti sesi belajar; tidak ada riwayat percakapan di WhatsApp.',
                'status' => DisputeStatus::UNDER_REVIEW,
            ],
        );

        // Sinkronkan rating & total ulasan
        ProviderProfile::all()->each->refreshRatingStats();
    }

    private function review(ProviderProfile $profile, User $client, int $rating, string $comment): Review
    {
        return Review::updateOrCreate(
            ['provider_profile_id' => $profile->id, 'client_id' => $client->id],
            ['rating' => $rating, 'comment' => $comment, 'is_published' => true],
        );
    }
}
