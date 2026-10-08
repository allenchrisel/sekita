<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(
            static fn (string $role) => UserRole::tryFrom(strtoupper($role)),
            $roles,
        );

        abort_unless(
            $request->user() && in_array($request->user()->role, $allowedRoles, true),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}