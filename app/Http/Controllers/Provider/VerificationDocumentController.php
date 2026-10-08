<?php

namespace App\Http\Controllers\Provider;

use App\Enums\DocumentType;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVerificationDocumentRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VerificationDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = $request->user()->verificationDocuments()->latest()->get();

        return view('provider.documents.index', compact('documents'));
    }

    public function store(StoreVerificationDocumentRequest $request): RedirectResponse
    {
        $user = $request->user();
        $type = DocumentType::from($request->validated('document_type'));
        $existing = $user->verificationDocuments()->where('document_type', $type->value)->first();
        $oldPath = $existing?->private_file_url;
        $extension = $request->file('document')->guessExtension() ?: 'bin';
        $path = $request->file('document')->storeAs(
            $user->id.'/'.$type->value,
            Str::uuid().'.'.$extension,
            'private_documents',
        );

        $user->verificationDocuments()->updateOrCreate(
            ['document_type' => $type],
            [
                'private_file_url' => $path,
                'status' => VerificationStatus::PENDING,
                'rejection_reason' => null,
            ],
        );

        if ($oldPath !== null) {
            Storage::disk('private_documents')->delete($oldPath);
        }

        if ($type === DocumentType::KTP) {
            $user->forceFill(['is_verified' => false])->save();
            $user->providerProfile?->forceFill(['id_verified_badge' => false])->save();
        }

        if ($type === DocumentType::IJAZAH) {
            $user->providerProfile?->forceFill(['degree_badge' => false])->save();
        }

        return back()->with('status', 'Dokumen terkirim dan menunggu verifikasi admin.');
    }
}