<?php

namespace App\Http\Controllers\Provider;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderReplyRequest;
use App\Http\Requests\StoreReviewDisputeRequest;
use App\Models\Review;
use App\Models\ReviewDispute;
use App\Models\ReviewReply;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->providerProfile()->firstOrFail();
        $reviews = $profile->reviews()->with(['client', 'reply', 'disputes'])->latest()->paginate(12);

        return view('provider.reviews.index', compact('reviews'));
    }

    public function reply(StoreProviderReplyRequest $request, Review $review): RedirectResponse
    {
        $profile = $request->user()->providerProfile()->firstOrFail();
        abort_unless($review->provider_profile_id === $profile->id, 404);

        ReviewReply::updateOrCreate(
            ['review_id' => $review->id],
            [
                'provider_profile_id' => $profile->id,
                'reply_text' => $request->validated('reply_text'),
            ],
        );

        return back()->with('status', 'Balasan publik berhasil disimpan.');
    }

    public function dispute(StoreReviewDisputeRequest $request, Review $review): RedirectResponse
    {
        $profile = $request->user()->providerProfile()->firstOrFail();
        abort_unless($review->provider_profile_id === $profile->id, 404);

        ReviewDispute::updateOrCreate(
            ['review_id' => $review->id, 'reporter_id' => $request->user()->id],
            [
                'reason' => $request->validated('reason'),
                'evidence_details' => $request->validated('evidence_details'),
                'status' => DisputeStatus::UNDER_REVIEW,
            ],
        );

        return back()->with('status', 'Laporan dikirim ke tim moderasi SeKita.');
    }
}