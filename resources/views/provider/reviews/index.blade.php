@extends('layouts.antari', ['title' => 'Kelola ulasan'])

@section('content')
    <section class="mx-auto max-w-5xl px-5 py-9 lg:px-8">
        <a href="{{ route('provider.dashboard') }}" class="text-sm font-bold text-[var(--muted)]">← Kembali ke dashboard</a>
        <div class="mt-5"><p class="text-xs font-bold uppercase text-[var(--coral)]">Percakapan pelanggan</p><h1 class="mt-2 font-display text-4xl">Ulasan & balasan</h1></div>
        <x-provider-workspace-nav active="reviews" />
        <div class="mt-7 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-5 sm:px-7">
            @forelse ($reviews as $review)
                <article class="py-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><p class="font-bold">{{ $review->client->name }} <span class="ml-2">★ {{ $review->rating }}</span></p><p class="mt-3 text-sm leading-6 text-[var(--muted)]">{{ $review->comment }}</p></div>
                        <span class="text-xs font-semibold text-[var(--muted)]">{{ $review->created_at->format('d M Y') }}</span>
                    </div>
                    @if ($review->reply)
                        <div class="mt-4 border-l-2 border-[var(--lime)] pl-4"><p class="text-xs font-bold uppercase text-[var(--muted)]">Balasan publik</p><p class="mt-1 text-sm">{{ $review->reply->reply_text }}</p></div>
                    @endif
                    <form method="POST" action="{{ route('provider.reviews.reply', $review) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                        @csrf @method('PUT')
                        <input class="field-control" name="reply_text" maxlength="500" value="{{ old('reply_text', $review->reply?->reply_text) }}" placeholder="Tulis balasan publik" required>
                        <button class="button-primary shrink-0" type="submit">{{ $review->reply ? 'Perbarui balasan' : 'Balas ulasan' }}</button>
                    </form>
                    <details class="mt-4 text-sm">
                        <summary class="cursor-pointer font-bold text-[var(--coral)]">Laporkan ulasan ini</summary>
                        <form method="POST" action="{{ route('provider.reviews.dispute', $review) }}" class="mt-3 grid gap-3 rounded-md bg-[var(--paper)] p-4">
                            @csrf
                            <label class="font-bold">Alasan laporan<input class="field-control mt-2 bg-white" name="reason" maxlength="160" required></label>
                            <label class="font-bold">Detail bukti<textarea class="field-control mt-2 bg-white" name="evidence_details" rows="4" maxlength="1500"></textarea></label>
                            <button class="button-secondary justify-self-start" type="submit">Kirim laporan ke admin</button>
                        </form>
                    </details>
                </article>
            @empty
                <p class="py-10 text-sm text-[var(--muted)]">Belum ada ulasan masuk.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $reviews->links() }}</div>
    </section>
@endsection