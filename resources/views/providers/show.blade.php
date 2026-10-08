@extends('layouts.antari', ['title' => $providerProfile->user->name])

@section('content')
    @php
        $phoneNumber = preg_replace('/\D+/', '', $providerProfile->whatsapp_number);
    @endphp

    <div class="mx-auto max-w-7xl px-5 py-8 lg:px-8">
        <a href="{{ route('home') }}" class="text-sm font-bold text-[var(--muted)] hover:text-[var(--green)]">← Kembali ke pencarian</a>
        <section class="mt-6 rounded-md border border-[var(--line)] bg-white">
            <div class="flex flex-col p-6 sm:p-9 lg:p-11">
                <div class="flex flex-wrap gap-2">
                    @if ($providerProfile->id_verified_badge)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">✓ Identitas KTP terverifikasi</span>
                    @endif
                    @if ($providerProfile->degree_badge)
                        <span class="rounded-full bg-lime-50 px-3 py-1 text-xs font-bold text-lime-900">✓ Ijazah S1 terverifikasi</span>
                    @endif
                </div>
                <p class="mt-6 text-sm font-semibold text-[var(--coral)]">{{ $providerProfile->title }}</p>
                <h1 class="mt-2 font-display text-4xl leading-tight sm:text-5xl">{{ $providerProfile->user->name }}</h1>
                @if ($providerProfile->user->last_online_at)
                    @if ($providerProfile->user->last_online_at->gte(now()->subMinutes(5)))
                        <p class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-800">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Aktif Sekarang
                        </p>
                    @else
                        <p class="mt-3 inline-flex items-center gap-2 text-sm text-[var(--muted)]">
                            <span class="h-2.5 w-2.5 rounded-full bg-gray-400" aria-hidden="true"></span>
                            Aktif {{ $providerProfile->user->last_online_at->diffForHumans() }}
                        </p>
                    @endif
                @else
                    <p class="mt-3 inline-flex items-center gap-2 text-sm text-[var(--muted)]">
                        <span class="h-2.5 w-2.5 rounded-full bg-gray-400" aria-hidden="true"></span>
                        Belum ada aktivitas
                    </p>
                @endif
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-[var(--muted)]">
                    <span class="font-bold text-[var(--ink)]">★ {{ number_format($providerProfile->avg_rating, 1) }}</span>
                    <span>{{ $providerProfile->total_reviews }} ulasan</span>
                    <span>{{ collect([$providerProfile->district?->name, $providerProfile->regency?->name, $providerProfile->province?->name])->filter()->implode(', ') ?: $providerProfile->address }}</span>
                </div>
                <p class="mt-6 text-sm leading-7 text-[var(--muted)]">{{ $providerProfile->bio ?: 'Penyedia jasa lokal di SeKita.' }}</p>
                @if ($providerProfile->address)<p class="mt-3 text-sm text-[var(--muted)]">Area rinci: {{ $providerProfile->address }}</p>@endif
                <div class="mt-6 border-t border-[var(--line)] pt-5">
                    <span class="text-xs font-semibold text-[var(--muted)]">Tarif awal</span>
                    <p class="mt-1 text-2xl font-bold">Rp {{ number_format($providerProfile->starting_price ?? 0, 0, ',', '.') }} <span class="text-sm font-medium text-[var(--muted)]">/ sesi atau pekerjaan</span></p>
                </div>
                <div class="mt-auto flex flex-wrap gap-3 pt-7">
                    <a class="button-primary" href="{{ $providerProfile->whatsappUrl() }}" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.2.3 11.7c0 2.1.6 4.1 1.6 5.9L0 24l6.6-1.7a12 12 0 0 0 5.5 1.4h.1c6.5 0 11.8-5.2 11.8-11.7 0-3.2-1.2-6.2-3.5-8.5ZM12.2 21.7c-1.7 0-3.4-.5-4.9-1.3l-.4-.2-3.9 1 1-3.8-.2-.4a9.7 9.7 0 0 1-1.5-5.2c0-5.4 4.4-9.8 9.8-9.8 2.6 0 5.1 1 6.9 2.9a9.7 9.7 0 0 1 2.9 6.9c0 5.4-4.4 9.8-9.7 9.8Zm5.4-7.3c-.3-.1-1.7-.8-2-1-.3-.1-.5-.1-.6.2-.2.3-.7 1-1 1.2-.1.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.5.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.1c-.2-.6-.5-.5-.7-.5h-.5c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.3s1.1 2.6 1.2 2.8c.1.2 2.1 3.2 5 4.4.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.1-.3-.2-.6-.4Z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                    {{-- <a class="button-secondary" href="tel:{{ $phoneNumber }}">Telepon langsung</a> --}}
                </div>
                <div class="mt-5 flex gap-4 text-sm font-semibold text-[var(--green)]">
                    @if ($providerProfile->instagram_url)
                        <a href="{{ $providerProfile->instagram_url }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram {{ $providerProfile->user->name }}" title="Instagram" class="inline-flex h-11 w-11 items-center justify-center rounded-md border border-[var(--line)] bg-white text-[var(--green)] transition hover:border-[var(--green)] hover:bg-[var(--paper)]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/></svg>
                        </a>
                    @endif
                    {{-- @if ($providerProfile->website_url)<a href="{{ $providerProfile->website_url }}" target="_blank" rel="noopener noreferrer">Website ↗</a>@endif --}}
                </div>
            </div>
        </section>

        <section class="mt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase text-[var(--coral)]">Pilihan karya</p>
                    <h2 class="mt-2 font-display text-3xl">Galeri pekerjaan</h2>
                </div>
                <span class="text-sm text-[var(--muted)]">{{ $providerProfile->portfolioGalleries->count() }} dari 6 foto</span>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($providerProfile->portfolioGalleries as $gallery)
                    @php($image = str_starts_with($gallery->image_url, 'http') ? $gallery->image_url : \Illuminate\Support\Facades\Storage::disk('public')->url($gallery->image_url))
                    <figure class="overflow-hidden rounded-md bg-white">
                        <img src="{{ $image }}" alt="{{ $gallery->caption ?: 'Contoh pekerjaan '.$providerProfile->user->name }}" class="aspect-[4/3] w-full image-cover" loading="lazy">
                        @if ($gallery->caption)<figcaption class="px-4 py-3 text-sm font-medium">{{ $gallery->caption }}</figcaption>@endif
                    </figure>
                @empty
                    <p class="col-span-full border-y border-[var(--line)] py-8 text-sm text-[var(--muted)]">Provider belum mengunggah foto pekerjaan.</p>
                @endforelse
            </div>
        </section>

        <section class="mt-14 grid gap-10 lg:grid-cols-[1fr_.8fr]">
            <div>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-[var(--coral)]">Dari pelanggan</p>
                        <h2 class="mt-2 font-display text-3xl">Ulasan & rating</h2>
                    </div>
                    <span class="font-bold">★ {{ number_format($providerProfile->avg_rating, 1) }}</span>
                </div>
                <div class="mt-5 divide-y divide-[var(--line)] border-y border-[var(--line)]">
                    @forelse ($reviews as $review)
                        <article class="py-5">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-bold">{{ $review->client->name }}</p>
                                <span class="text-sm font-bold">{{ str_repeat('★', $review->rating) }}<span class="text-[var(--line)]">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-[var(--muted)]">{{ $review->comment }}</p>
                            @if ($review->reply)
                                <div class="mt-4 border-l-2 border-[var(--lime)] pl-4 text-sm">
                                    <p class="font-bold">Balasan {{ $providerProfile->user->name }}</p>
                                    <p class="mt-1 text-[var(--muted)]">{{ $review->reply->reply_text }}</p>
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="py-7 text-sm text-[var(--muted)]">Belum ada ulasan untuk penyedia ini.</p>
                    @endforelse
                </div>
                <div class="mt-5">{{ $reviews->links() }}</div>
            </div>

            <aside class="h-fit rounded-md border border-[var(--line)] bg-white p-5 sm:p-6">
                <h2 class="font-display text-2xl">Pernah memakai jasanya?</h2>
                @auth
                    @if (auth()->user()->isClient() && auth()->user()->is_verified)
                        <form method="POST" action="{{ route('reviews.store', ['providerProfile' => $providerProfile->slug]) }}" class="mt-5 space-y-4">
                            @csrf
                            <label class="block text-sm font-bold">Rating
                                <select name="rating" class="field-control mt-2" required>
                                    <option value="5">★★★★★ · Sangat puas</option>
                                    <option value="4">★★★★☆ · Puas</option>
                                    <option value="3">★★★☆☆ · Cukup</option>
                                    <option value="2">★★☆☆☆ · Kurang</option>
                                    <option value="1">★☆☆☆☆ · Tidak puas</option>
                                </select>
                            </label>
                            <label class="block text-sm font-bold">Pengalaman Anda
                                <textarea name="comment" rows="4" maxlength="500" class="field-control mt-2" placeholder="Bagikan pengalaman dengan jujur" required></textarea>
                            </label>
                            <button class="button-primary w-full" type="submit">Kirim ulasan</button>
                        </form>
                    @elseif (auth()->user()->isClient())
                        <p class="mt-3 text-sm leading-6 text-[var(--muted)]">Akun Anda perlu diverifikasi sebelum mengirim ulasan.</p>
                    @else
                        <p class="mt-3 text-sm leading-6 text-[var(--muted)]">Ulasan hanya dapat dikirim oleh akun pencari jasa yang terverifikasi.</p>
                    @endif
                @else
                    <p class="mt-3 text-sm leading-6 text-[var(--muted)]">Masuk dengan akun terverifikasi untuk berbagi pengalaman.</p>
                    <a href="{{ route('login') }}" class="button-primary mt-5 w-full">Masuk untuk mengulas</a>
                @endauth
            </aside>
        </section>
    </div>
@endsection