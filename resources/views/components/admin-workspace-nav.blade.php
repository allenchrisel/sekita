@props(['active'])

<nav class="mt-7 flex gap-2 overflow-x-auto border-b border-[var(--line)] pb-3 text-sm font-bold" aria-label="Navigasi panel admin">
    @foreach ([
        'dashboard' => ['Ringkasan', 'admin.dashboard'],
        'documents' => ['Dokumen', 'admin.documents.index'],
        'disputes' => ['Laporan ulasan', 'admin.disputes.index'],
        'users' => ['Pengguna', 'admin.users.index'],
    ] as $key => [$label, $route])
        <a @class([
            'shrink-0 rounded-full px-4 py-2',
            'bg-[var(--green)] text-white' => $active === $key,
            'hover:bg-white' => $active !== $key,
        ]) href="{{ route($route) }}" @if ($active === $key) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
