<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastOnline
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->role === UserRole::PROVIDER) {
            $now = now();

            DB::table('users')
                ->where('id', $user->id)
                ->where(function ($query) use ($now): void {
                    $query->whereNull('last_online_at')
                        ->orWhere('last_online_at', '<=', $now->copy()->subMinute());
                })
                ->update(['last_online_at' => $now]);
        }

        return $next($request);
    }
}