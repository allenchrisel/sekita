@extends('layouts.antari', ['title' => 'Manajemen pengguna'])

@section('content')
    <section class="mx-auto max-w-7xl px-5 py-9 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase text-[var(--coral)]">Administrasi</p>
                <h1 class="mt-2 font-display text-4xl">Manajemen pengguna</h1>
            </div>
            <p class="text-sm text-[var(--muted)]">{{ $users->total() }} akun client/provider</p>
        </div>
        <x-admin-workspace-nav active="users" />

        <form method="GET" action="{{ route('admin.users.index') }}" class="mt-6 grid gap-3 rounded-md border border-[var(--line)] bg-white p-4 sm:grid-cols-[1fr_13rem_auto]">
            <label class="sr-only" for="user-search">Cari nama atau email</label>
            <input id="user-search" class="field-control" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email">
            <label class="sr-only" for="user-role">Filter jenis akun</label>
            <select id="user-role" class="field-control" name="role">
                <option value="">Semua akun</option>
                <option value="PROVIDER" @selected(($filters['role'] ?? '') === 'PROVIDER')>Penyedia jasa</option>
                <option value="CLIENT" @selected(($filters['role'] ?? '') === 'CLIENT')>Pencari jasa</option>
            </select>
            <button class="button-primary" type="submit">Terapkan</button>
        </form>

        <div class="mt-6 overflow-x-auto rounded-md border border-[var(--line)] bg-white">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="border-b border-[var(--line)] bg-[#f0f4ef] text-xs uppercase text-[var(--muted)]">
                    <tr><th class="px-5 py-4">Pengguna</th><th class="px-5 py-4">Jenis akun</th><th class="px-5 py-4">Bergabung</th><th class="px-5 py-4">Tindakan</th></tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-bold">{{ $user->name }}</p>
                                <p class="mt-1 text-xs text-[var(--muted)]">{{ $user->email }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $user->role->label() }}</td>
                            <td class="px-5 py-4">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus akun {{ $user->name }} secara permanen beserta profil, ulasan, dokumen, dan file terkait? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-md bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-800" type="submit">Hapus Akun</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-[var(--muted)]">Tidak ada akun yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </section>
@endsection
