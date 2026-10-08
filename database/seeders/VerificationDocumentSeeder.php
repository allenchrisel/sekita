<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\VerificationDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class VerificationDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/private_documents'),
            'visibility' => 'private',
            'throw' => true,
        ]);

        $documents = [
            ['ahmad.provider@antari.id', DocumentType::KTP, VerificationStatus::VERIFIED, null],
            ['ahmad.provider@antari.id', DocumentType::IJAZAH, VerificationStatus::PENDING, null],
            ['siti.provider@antari.id', DocumentType::KTP, VerificationStatus::VERIFIED, null],
            ['dedi.provider@antari.id', DocumentType::KTP, VerificationStatus::REJECTED, 'Foto KTP buram dan sebagian data tidak terbaca. Silakan unggah ulang.'],
        ];

        foreach ($documents as [$email, $type, $status, $reason]) {
            $user = User::where('email', $email)->firstOrFail();
            $path = 'dummy/'.$user->id.'_'.strtolower($type->value).'.txt';

            // File dummy (bukan dokumen asli) agar alur unduh via Controller Admin dapat diuji.
            $disk->put($path, "DUMMY {$type->value} untuk {$user->email} - hanya data pengujian.");

            VerificationDocument::updateOrCreate(
                ['user_id' => $user->id, 'document_type' => $type],
                [
                    'private_file_url' => $path,
                    'status' => $status,
                    'rejection_reason' => $reason,
                ],
            );
        }
    }
}
