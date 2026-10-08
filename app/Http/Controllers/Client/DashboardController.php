<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $reviews = $request->user()->reviewsWritten()
            ->with('providerProfile.user')
            ->latest()
            ->paginate(10);

        return view('client.dashboard', compact('reviews'));
    }
}