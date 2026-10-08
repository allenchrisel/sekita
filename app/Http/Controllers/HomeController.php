<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Province;
use App\Models\ProviderProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:120'],
            'province_code' => ['nullable', 'string', Rule::exists('provinces', 'code')],
            'regency_code' => ['nullable', 'string', 'required_with:district_code', Rule::exists('regencies', 'code')->where('province_code', $request->input('province_code'))],
            'district_code' => ['nullable', 'string', Rule::exists('districts', 'code')->where('regency_code', $request->input('regency_code'))],
        ]);

        $query = ProviderProfile::query()
            ->whereNotNull('province_code')
            ->with(['user', 'category', 'portfolioGalleries', 'province', 'regency', 'district']);

        if (! empty($filters['q'])) {
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim(strip_tags($filters['q']))).'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('title', 'like', $term)
                    ->orWhere('bio', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $term))
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', $term));
            });
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($category) => $category->where('slug', $filters['category']));
        }

        foreach (['province_code', 'regency_code', 'district_code'] as $locationField) {
            if (! empty($filters[$locationField])) {
                $query->where($locationField, $filters[$locationField]);
            }
        }

        $query->orderByDesc('id_verified_badge')->orderByDesc('avg_rating');

        $perPage = 12;
        $paginatedProviders = $query->paginate($perPage)->withQueryString()->fragment('providers');

        return view('home', [
            'providers' => $paginatedProviders,
            'categories' => Category::query()->orderBy('name')->get(),
            'provinces' => Province::query()->orderBy('name')->get(['code', 'name']),
            'filters' => $filters,
        ]);
    }
}