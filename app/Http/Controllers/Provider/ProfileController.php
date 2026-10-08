<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProviderProfileRequest;
use App\Models\Category;
use App\Models\Province;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('provider.profile.edit', [
            'profile' => $request->user()->providerProfile()->with('portfolioGalleries')->firstOrFail(),
            'categories' => Category::query()->orderBy('name')->get(),
            'provinces' => Province::query()->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function update(UpdateProviderProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->providerProfile()->firstOrFail();
        $profile->fill($request->validated())->save();

        return back()->with('status', 'Profil jasa berhasil diperbarui.');
    }
}