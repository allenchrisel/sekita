@extends('layouts.antari', ['title' => 'Dashboard penyedia'])

@section('content')
    <section class="mx-auto max-w-7xl px-5 py-9 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase text-[var(--coral)]">Ruang kerja penyedia</p>
                <h1 class="mt-2 font-display text-4xl">Selamat datang, {{ auth()->user()->name }}</h1>
                <p class="mt-2 text-sm text-[var(--muted)]">Kelola profil, bukti kerja, dan percakapan pelanggan.</p>
            </div>
            <a href="{{ route('providers.show', ['providerProfile' => $profile->slug]) }}" class="button-secondary" target="_blank">Lihat profil publik ↗</a>
        </div>

        <x-provider-workspace-nav active="dashboard" />

        <div class="mt-7 grid gap-5 lg:grid-cols-[1.2fr_.8fr]">
            <section class="rounded-md border border-[var(--line)] bg-white p-5 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-[var(--muted)]">Profil jasa</p>
                        <h2 class="mt-2 font-display text-3xl">{{ $profile->title }}</h2>
                        <p class="mt-2 text-sm text-[var(--muted)]">{{ $profile->category->name }} · {{ collect([$profile->district?->name, $profile->regency?->name, $profile->province?->name])->filter()->implode(', ') }}</p>
                    </div>
                    <a href="{{ route('provider.profile.edit') }}" class="button-secondary">Edit profil</a>
                </div>
                <p class="mt-5 max-w-3xl text-sm leading-7 text-[var(--muted)]">{{ $profile->bio ?: 'Tambahkan ringkasan pengalaman untuk membantu pelanggan mengenal jasa Anda.' }}</p>
                <div class="mt-6 grid gap-3 border-t border-[var(--line)] pt-5 sm:grid-cols-3">
                    <div><p class="text-xs text-[var(--muted)]">Rating publik</p><p class="mt-1 font-bold">★ {{ number_format($profile->avg_rating, 1) }} <span class="font-normal text-[var(--muted)]">({{ $profile->total_reviews }})</span></p></div>
                    <div><p class="text-xs text-[var(--muted)]">Tarif awal</p><p class="mt-1 font-bold">Rp {{ number_format($profile->starting_price ?? 0, 0, ',', '.') }}</p></div>
                    <div><p class="text-xs text-[var(--muted)]">Portofolio</p><p class="mt-1 font-bold">{{ $profile->portfolioGalleries->count() }} / 6 foto</p></div>
                </div>
            </section>
            <section class="rounded-md border border-[var(--line)] bg-white p-5 sm:p-7">
                <p class="text-xs font-bold uppercase text-[var(--muted)]">Verifikasi identitas</p>
                <h2 class="mt-2 font-display text-2xl">Lencana profil</h2>
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between border-b border-[var(--line)] pb-3"><span>Identitas KTP</span><span class="font-bold {{ $profile->id_verified_badge ? 'text-[var(--green)]' : 'text-[var(--muted)]' }}">{{ $profile->id_verified_badge ? '✓ Terverifikasi' : 'Menunggu dokumen' }}</span></div>
                    <div class="flex items-center justify-between border-b border-[var(--line)] pb-3"><span>Ijazah</span><span class="font-bold {{ $profile->degree_badge ? 'text-[var(--green)]' : 'text-[var(--muted)]' }}">{{ $profile->degree_badge ? '✓ Terverifikasi' : 'Belum terverifikasi' }}</span></div>
                </div>
                <a href="{{ route('provider.documents.index') }}" class="mt-5 inline-flex font-bold text-[var(--green)]">Kelola dokumen →</a>
            </section>
        </div>

        <section class="mt-8">
            <div class="flex items-end justify-between gap-4">
                <div><p class="text-xs font-bold uppercase text-[var(--coral)]">Terbaru</p><h2 class="mt-2 font-display text-3xl">Ulasan pelanggan</h2></div>
                <a href="{{ route('provider.reviews.index') }}" class="text-sm font-bold text-[var(--green)]">Kelola ulasan →</a>
            </div>
            <div class="mt-4 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-5">
                @forelse ($reviews as $review)
                    <article class="flex flex-wrap items-start justify-between gap-3 py-4">
                        <div><p class="font-bold">{{ $review->client->name }} <span class="ml-2 text-sm">★ {{ $review->rating }}</span></p><p class="mt-1 text-sm text-[var(--muted)]">{{ $review->comment }}</p></div>
                        <span class="text-xs font-semibold text-[var(--muted)]">{{ $review->reply ? 'Sudah dibalas' : 'Belum dibalas' }}</span>
                    </article>
                @empty
                    <p class="py-6 text-sm text-[var(--muted)]">Belum ada ulasan.</p>
                @endforelse
            </div>
        </section>
    </section>
@endsection