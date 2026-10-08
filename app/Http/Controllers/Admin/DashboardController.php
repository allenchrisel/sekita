<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\ReviewDispute;
use App\Models\VerificationDocument;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $pendingDocuments = VerificationDocument::pending()->with('user')->latest()->limit(6)->get();
        $pendingDisputes = ReviewDispute::underReview()->with(['review.client', 'reporter'])->latest()->limit(6)->get();

        return view('admin.dashboard', [
            'pendingDocuments' => $pendingDocuments,
            'pendingDisputes' => $pendingDisputes,
            'documentCount' => VerificationDocument::where('status', VerificationStatus::PENDING)->count(),
            'disputeCount' => ReviewDispute::underReview()->count(),
        ]);
    }
}