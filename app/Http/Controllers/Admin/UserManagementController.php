<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:CLIENT,PROVIDER'],
        ]);

        $users = User::query()
            ->whereIn('role', [UserRole::CLIENT, UserRole::PROVIDER])
            ->with('providerProfile')
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->where('role', $role))
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($search) use ($term): void {
                    $search->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, [UserRole::CLIENT, UserRole::PROVIDER], true), 404);
        abort_if($request->user()->is($user), 403);

        $storedPrivateFiles = $user->verificationDocuments()->pluck('private_file_url')->all();
        $privateFiles = collect($storedPrivateFiles)
            ->filter(fn (string $path): bool => preg_match(
                '/^'.preg_quote((string) $user->id, '/').'\/[A-Z_]+\/[A-Za-z0-9._-]+$/D',
                $path,
            ) === 1 && ! str_contains($path, '..'))
            ->values()
            ->all();
        $storedPublicFiles = $user->providerProfile?->portfolioGalleries()
            ->pluck('image_url')
            ->reject(fn (string $path): bool => preg_match('/^https?:\/\//i', $path) === 1)
            ->all() ?? [];
        $publicFiles = collect($storedPublicFiles)
            ->filter(fn (string $path): bool => preg_match(
                '/^portfolios\/'.preg_quote((string) $user->id, '/').'\/[A-Za-z0-9._-]+$/D',
                $path,
            ) === 1 && ! str_contains($path, '..'))
            ->values()
            ->all();

        DB::transaction(fn () => $user->delete());

        $privateFilesDeleted = $privateFiles === []
            || Storage::disk('private_documents')->delete($privateFiles);
        $publicFilesDeleted = $publicFiles === []
            || Storage::disk('public')->delete($publicFiles);

        $allFilesDeleted = $privateFilesDeleted
            && $publicFilesDeleted
            && count($privateFiles) === count($storedPrivateFiles)
            && count($publicFiles) === count($storedPublicFiles);

        if (! $allFilesDeleted) {
            Log::warning('Failed to remove some account files after deleting user.', [
                'user_id' => $user->id,
                'private_files_deleted' => $privateFilesDeleted,
                'public_files_deleted' => $publicFilesDeleted,
                'private_files_skipped' => count($storedPrivateFiles) - count($privateFiles),
                'public_files_skipped' => count($storedPublicFiles) - count($publicFiles),
            ]);
        }

        $status = 'Akun pengguna berhasil dihapus.';
        if (! $allFilesDeleted) {
            $status .= ' Sebagian file tidak dapat dibersihkan dan telah dicatat untuk pemeriksaan.';
        }

        return to_route('admin.users.index')->with('status', $status);
    }
}
