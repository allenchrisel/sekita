@props(['active'])

<nav class="mt-7 flex gap-2 overflow-x-auto border-b border-[var(--line)] pb-3 text-sm font-bold" aria-label="Navigasi ruang kerja penyedia">
    @foreach ([
        'dashboard' => ['Ringkasan', 'provider.dashboard'],
        'profile' => ['Profil jasa', 'provider.profile.edit'],
        'documents' => ['Dokumen', 'provider.documents.index'],
        'reviews' => ['Ulasan', 'provider.reviews.index'],
    ] as $key => [$label, $route])
        <a @class([
            'shrink-0 rounded-full px-4 py-2',
            'bg-[var(--green)] text-white' => $active === $key,
            'hover:bg-white' => $active !== $key,
        ]) href="{{ route($route) }}" @if ($active === $key) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
