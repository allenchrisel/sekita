@extends('layouts.antari', ['title' => 'Jasa lokal, langsung terhubung'])

@section('content')
    <section class="relative overflow-hidden bg-[var(--ink)] text-white">
        <div class="mx-auto grid max-w-7xl items-center gap-8 px-5 py-12 sm:py-16 lg:grid-cols-[1.08fr_.92fr] lg:px-8 lg:py-20">
            <div class="relative z-10 max-w-2xl">
                <p class="mb-5 inline-flex items-center gap-2 text-xs font-bold uppercase text-[var(--lime)]">
                    <span class="h-2 w-2 rounded-full bg-[var(--lime)]"></span> Koneksi jasa di sekitar Anda
                </p>
                <h3 class="font-display text-3xl leading-[1.05] sm:text-5xl">Butuh bantuan?<br><span class="text-[var(--lime)]"> Cari yang ada di Sekitar Kita</span></h3>
                <p class="mt-5 max-w-lg text-base leading-7 text-white/75">Temukan tenaga ahli lokal untuk berbagai kebutuhan, lihat portofolionya, dan hubungi langsung</p>
                <a href="#temukan" class="mt-7 inline-flex items-center gap-2 font-bold text-[var(--lime)] hover:underline">
                    Temukan penyedia jasa <span aria-hidden="true">↓</span>
                </a>
            </div>
            <div
                class="relative min-h-[270px] overflow-hidden rounded-md bg-black sm:min-h-[320px] lg:min-h-[390px]"
                x-data="{
                    activeSlide: 0,
                    imageVisible: true,
                    paused: false,
                    timer: null,
                    slides: @js($categories->map(fn ($category) => [
                        'name' => $category->name,
                        'description' => $category->description,
                        'image' => $category->cover_image,
                        'url' => route('home', ['category' => $category->slug]),
                    ])->values()),
                    nextSlide() { this.imageVisible = false; this.activeSlide = (this.activeSlide + 1) % this.slides.length },
                    previousSlide() { this.imageVisible = false; this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
                }"
                x-init="timer = setInterval(() => { if (!paused) nextSlide() }, 5000)"
                x-on:mouseenter="paused = true"
                x-on:mouseleave="paused = false"
                x-on:focusin="paused = true"
                x-on:focusout="paused = false"
                aria-label="Jelajahi kategori jasa"
                aria-roledescription="carousel"
            >
                <div class="absolute inset-0">
                    <img
                        src="{{ $categories->first()?->cover_image }}"
                        :src="slides[activeSlide].image"
                        :alt="'Kategori jasa ' + slides[activeSlide].name"
                        class="absolute inset-0 h-full w-full image-cover transition-opacity duration-500"
                        :class="imageVisible ? 'opacity-100' : 'opacity-0'"
                        x-on:load="imageVisible = true"
                        fetchpriority="high"
                    >
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-5 text-white sm:p-7">
                    <div class="min-w-0 drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                        <h2 class="mt-1 font-display text-2xl sm:text-3xl" x-text="slides[activeSlide].name"></h2>
                        <p class="mt-1 line-clamp-2 max-w-md text-sm text-white/90" x-text="slides[activeSlide].description"></p>
                        <a class="mt-3 inline-grid h-10 w-10 place-items-center rounded-md border border-white/50 bg-black/30 text-[var(--lime)] backdrop-blur-sm transition hover:bg-white hover:text-[var(--ink)]" :href="slides[activeSlide].url" :aria-label="'Lihat kategori ' + slides[activeSlide].name" title="Jelajahi kategori">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <button type="button" class="grid h-10 w-10 place-items-center rounded-md border border-white/50 bg-black/30 text-white backdrop-blur-sm transition hover:bg-white hover:text-[var(--ink)]" x-on:click="previousSlide()" aria-label="Foto kategori sebelumnya" title="Sebelumnya">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" class="grid h-10 w-10 place-items-center rounded-md border border-white/50 bg-black/30 text-white backdrop-blur-sm transition hover:bg-white hover:text-[var(--ink)]" x-on:click="nextSlide()" aria-label="Foto kategori berikutnya" title="Berikutnya">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="temukan" class="mx-auto -mt-1 max-w-7xl px-5 pt-8 lg:px-8">
        <form method="GET" action="{{ route('home') }}" data-turbo-frame="provider-results" class="grid gap-3 rounded-md border border-[var(--line)] bg-white p-4 shadow-sm md:grid-cols-[1.2fr_1fr_3fr_auto] md:items-end md:p-5">
            <label class="block text-xs font-bold text-[var(--muted)]">Jasa atau keahlian
                <input class="field-control mt-2" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: guru matematika">
            </label>
            <label class="block text-xs font-bold text-[var(--muted)]">Kategori
                <select class="field-control mt-2" name="category">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <x-location-picker
                :provinces="$provinces"
                :selected-province="$filters['province_code'] ?? ''"
                :selected-regency="$filters['regency_code'] ?? ''"
                :selected-district="$filters['district_code'] ?? ''"
                class="md:col-span-1"
            />
            <button class="button-primary" type="submit">Cari jasa</button>
        </form>
        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-[var(--muted)]">
            <span class="mr-1 font-bold">Sering dicari</span>
            @foreach (['Guru les', 'Teknisi AC', 'Bersih rumah', 'Renovasi'] as $term)
                <a class="rounded-full border border-[var(--line)] bg-white px-3 py-1.5 hover:border-[var(--green)] hover:text-[var(--green)]" href="{{ route('home', ['q' => $term]) }}" data-turbo-frame="provider-results">{{ $term }}</a>
            @endforeach
        </div>
    </section>

    <turbo-frame id="provider-results" data-turbo-action="advance">
    <section id="providers" class="mx-auto max-w-7xl scroll-mt-6 px-5 pt-12 lg:px-8 lg:pt-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase text-[var(--coral)]">Pilihan komunitas</p>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl">Temukan orang yang tepat.</h2>
            </div>
            <p class="max-w-sm text-sm leading-6 text-[var(--muted)]">Profil nyata, ulasan dari pelanggan, dan kontak langsung tanpa perantara.</p>
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($providers as $provider)
                @php
                    $providerLocation = collect([$provider->district?->name, $provider->regency?->name, $provider->province?->name])->filter()->implode(', ');
                @endphp
                <article class="overflow-hidden rounded-md border border-[var(--line)] bg-white transition hover:-translate-y-0.5 hover:shadow-md">
                    <a href="{{ route('providers.show', ['providerProfile' => $provider->slug]) }}" class="group block" data-turbo-frame="_top">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold group-hover:text-[var(--green)]">{{ $provider->user->name }}</h3>
                                    <p class="mt-1 text-sm text-[var(--muted)]">{{ $provider->title }}</p>
                                </div>
                                <span class="shrink-0 text-sm font-bold">★ {{ number_format($provider->avg_rating, 1) }}</span>
                            </div>
                            @if ($provider->id_verified_badge)
                                <p class="mt-3 text-xs font-bold text-[var(--green)]">✓ Identitas terverifikasi</p>
                            @endif
                            <div class="mt-4 border-t border-[var(--line)] pt-4">
                                <p class="text-xs font-semibold text-[var(--muted)]">{{ $providerLocation ?: $provider->address }}</p>
                                <p class="mt-3 text-xs text-[var(--muted)]">Tarif mulai dari</p>
                                <p class="mt-1 font-bold">Rp {{ number_format($provider->starting_price ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="col-span-full border-y border-[var(--line)] py-12 text-center">
                    <p class="font-display text-2xl">Belum ada penyedia yang cocok.</p>
                    <p class="mt-2 text-sm text-[var(--muted)]">Coba ubah kata kunci, kategori, atau lokasi pencarian.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $providers->links() }}</div>
    </section>
    </turbo-frame>
@endsection