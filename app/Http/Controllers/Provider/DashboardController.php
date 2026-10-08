<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $profile = $request->user()->providerProfile()
            ->with(['category', 'portfolioGalleries', 'province', 'regency', 'district'])
            ->firstOrFail();

        $reviews = $profile->reviews()->with(['client', 'reply'])->latest()->limit(5)->get();
        $documents = $request->user()->verificationDocuments()->latest()->get();

        return view('provider.dashboard', compact('profile', 'reviews', 'documents'));
    }
}