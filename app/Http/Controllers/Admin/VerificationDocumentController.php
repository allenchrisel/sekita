<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentType;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewVerificationDocumentRequest;
use App\Models\VerificationDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VerificationDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => ['nullable', Rule::enum(DocumentType::class)],
        ]);

        $query = VerificationDocument::with('user')->latest();

        if (! empty($filters['type'])) {
            $query->where('document_type', $filters['type']);
        }

        $documents = $query->paginate(20)->withQueryString();
        $documentCounts = collect(DocumentType::cases())
            ->mapWithKeys(fn (DocumentType $type) => [$type->value => VerificationDocument::where('document_type', $type)->count()]);

        return view('admin.documents.index', [
            'documents' => $documents,
            'documentCounts' => $documentCounts,
            'selectedType' => $filters['type'] ?? null,
        ]);
    }

    public function download(VerificationDocument $verificationDocument)
    {
        abort_unless(Storage::disk('private_documents')->exists($verificationDocument->private_file_url), 404);

        return response()->download(
            Storage::disk('private_documents')->path($verificationDocument->private_file_url),
            $verificationDocument->document_type->value.'_user_'.$verificationDocument->user_id.'.'.pathinfo($verificationDocument->private_file_url, PATHINFO_EXTENSION),
        );
    }

    public function update(ReviewVerificationDocumentRequest $request, VerificationDocument $verificationDocument): RedirectResponse
    {
        DB::transaction(function () use ($request, $verificationDocument): void {
            $status = VerificationStatus::from($request->validated('status'));
            $verificationDocument->update([
                'status' => $status,
                'reviewed_at' => now(),
                'rejection_reason' => $status === VerificationStatus::REJECTED
                    ? $request->validated('rejection_reason')
                    : null,
            ]);

            $user = $verificationDocument->user;
            $profile = $user->providerProfile;

            if ($verificationDocument->document_type === DocumentType::KTP) {
                $user->forceFill(['is_verified' => $status === VerificationStatus::VERIFIED])->save();
                $profile?->forceFill(['id_verified_badge' => $status === VerificationStatus::VERIFIED])->save();
            }

            if ($verificationDocument->document_type === DocumentType::IJAZAH) {
                $profile?->forceFill(['degree_badge' => $status === VerificationStatus::VERIFIED])->save();
            }
        });

        return back()->with('status', 'Status dokumen berhasil diperbarui.');
    }
}