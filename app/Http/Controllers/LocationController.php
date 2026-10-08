<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Regency;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function regencies(string $provinceCode): JsonResponse
    {
        abort_unless(preg_match('/^[0-9.]+$/', $provinceCode) === 1, 404);

        return response()->json([
            'data' => Regency::query()
                ->where('province_code', $provinceCode)
                ->orderBy('name')
                ->get(['code', 'name']),
        ]);
    }

    public function districts(string $regencyCode): JsonResponse
    {
        abort_unless(preg_match('/^[0-9.]+$/', $regencyCode) === 1, 404);

        return response()->json([
            'data' => District::query()
                ->where('regency_code', $regencyCode)
                ->orderBy('name')
                ->get(['code', 'name']),
        ]);
    }
}