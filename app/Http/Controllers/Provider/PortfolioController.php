<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioImageRequest;
use App\Models\PortfolioGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;
use Throwable;

class PortfolioController extends Controller
{
    public function store(StorePortfolioImageRequest $request): RedirectResponse
    {
        $user = $request->user();
        $path = null;

        try {
            DB::transaction(function () use ($request, $user, &$path): void {
                $profile = $user->providerProfile()->lockForUpdate()->firstOrFail();

                if ($profile->portfolioGalleries()->count() >= PortfolioGallery::MAX_PHOTOS_PER_PROVIDER) {
                    abort(422, 'Galeri hanya dapat berisi maksimal enam foto.');
                }

                $encodedImage = Image::read($request->file('image')->getRealPath())
                    ->scaleDown(1600, 1200)
                    ->toWebp(82);

                $path = 'portfolios/'.$user->id.'/'.Str::uuid().'.webp';

                if (! Storage::disk('public')->put($path, $encodedImage->toString())) {
                    throw new RuntimeException('Foto tidak dapat disimpan.');
                }

                $profile->portfolioGalleries()->create([
                    'image_url' => $path,
                    'caption' => $request->validated('caption'),
                    'sort_order' => $profile->portfolioGalleries()->count(),
                ]);
            });
        } catch (Throwable $exception) {
            if ($path !== null) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return back()->with('status', 'Foto berhasil ditambahkan ke portofolio.');
    }

    public function destroy(Request $request, PortfolioGallery $portfolioGallery): RedirectResponse
    {
        $profile = $request->user()->providerProfile()->firstOrFail();
        abort_unless($portfolioGallery->provider_profile_id === $profile->id, 404);

        if (! str_starts_with($portfolioGallery->image_url, 'http')) {
            Storage::disk('public')->delete($portfolioGallery->image_url);
        }

        $portfolioGallery->delete();

        return back()->with('status', 'Foto berhasil dihapus.');
    }
}
