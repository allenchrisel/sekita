<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewDisputeDecisionRequest;
use App\Models\ReviewDispute;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewDisputeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(DisputeStatus::class)],
        ]);

        $query = ReviewDispute::with(['review.providerProfile.user', 'review.client', 'reporter'])->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $disputes = $query->paginate(20)->withQueryString();
        $disputeCounts = collect(DisputeStatus::cases())
            ->mapWithKeys(fn (DisputeStatus $status) => [$status->value => ReviewDispute::where('status', $status)->count()]);

        return view('admin.disputes.index', [
            'disputes' => $disputes,
            'disputeCounts' => $disputeCounts,
            'selectedStatus' => $filters['status'] ?? null,
        ]);
    }

    public function update(ReviewDisputeDecisionRequest $request, ReviewDispute $reviewDispute): RedirectResponse
    {
        DB::transaction(function () use ($request, $reviewDispute): void {
            $status = DisputeStatus::from($request->validated('status'));
            $reviewDispute->update([
                'status' => $status,
                'resolved_at' => now(),
            ]);
            $review = $reviewDispute->review;
            $review->update(['is_published' => $status === DisputeStatus::REJECTED]);
            $review->providerProfile->refreshRatingStats();
        });

        return back()->with('status', 'Keputusan laporan ulasan berhasil disimpan.');
    }
}