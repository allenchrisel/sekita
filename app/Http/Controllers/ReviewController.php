<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\ProviderProfile;
use App\Models\Review;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, ProviderProfile $providerProfile): RedirectResponse
    {
        abort_unless($request->user()->is_verified, 403, 'Akun Anda belum diverifikasi untuk mengirim ulasan.');

        try {
            Cache::lock("review:{$request->user()->id}:{$providerProfile->id}", 8)->block(3, function () use ($request, $providerProfile): void {
                DB::transaction(function () use ($request, $providerProfile): void {
                    $recentReviewExists = Review::withTrashed()
                        ->where('provider_profile_id', $providerProfile->id)
                        ->where('client_id', $request->user()->id)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->exists();

                    if ($recentReviewExists) {
                        throw ValidationException::withMessages([
                            'comment' => 'Anda dapat mengulas penyedia yang sama kembali setelah 30 hari.',
                        ]);
                    }

                    $providerProfile->reviews()->create([
                        'client_id' => $request->user()->id,
                        'rating' => $request->validated('rating'),
                        'comment' => $request->validated('comment'),
                        'is_published' => true,
                    ]);

                    $providerProfile->refreshRatingStats();
                });
            });
        } catch (LockTimeoutException) {
            abort(429, 'Permintaan ulasan sedang diproses. Coba kembali sebentar lagi.');
        }

        return back()->with('status', 'Ulasan Anda berhasil diterbitkan.');
    }

    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorizeClientReviewWindow($request, $review);

        $wasPublished = $review->is_published;
        $review->update([
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
            'is_published' => $wasPublished,
        ]);

        $review->providerProfile->refreshRatingStats();

        return back()->with('status', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        $this->authorizeClientReviewWindow($request, $review);
        $providerProfile = $review->providerProfile;
        $review->delete();
        $providerProfile->refreshRatingStats();

        return back()->with('status', 'Ulasan berhasil dihapus. Anda tetap dapat mengirim ulasan untuk penyedia ini setelah 30 hari sejak ulasan awal.');
    }

    private function authorizeClientReviewWindow(Request $request, Review $review): void
    {
        abort_unless($review->client_id === $request->user()->id, 404);

        if ($review->created_at->lt(now()->subDays(30))) {
            throw ValidationException::withMessages([
                'comment' => 'Ulasan hanya dapat diedit atau dihapus dalam 30 hari setelah diajukan.',
            ]);
        }
    }
}