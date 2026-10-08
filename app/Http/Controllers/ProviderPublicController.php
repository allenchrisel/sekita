<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use Illuminate\Contracts\View\View;

class ProviderPublicController extends Controller
{
    public function show(ProviderProfile $providerProfile): View
    {
        $providerProfile->load(['user', 'category', 'portfolioGalleries', 'province', 'regency', 'district']);

        $reviews = $providerProfile->publishedReviews()
            ->with(['client', 'reply'])
            ->latest()
            ->paginate(8);

        return view('providers.show', compact('providerProfile', 'reviews'));
    }
}