@extends('layouts.antari', ['title' => 'Ulasan saya'])

@section('content')
    <section class="mx-auto max-w-5xl px-5 py-10 lg:px-8">
        <p class="text-xs font-bold uppercase text-[var(--coral)]">Akun pencari jasa</p>
        <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl">Ulasan saya</h1>
                <p class="mt-2 text-sm text-[var(--muted)]">Riwayat ulasan yang pernah Anda bagikan.</p>
            </div>
            <a href="{{ route('home') }}#temukan" class="button-primary">Cari jasa</a>
        </div>
        <div class="mt-8 divide-y divide-[var(--line)] border-y border-[var(--line)]">
            @forelse ($reviews as $review)
                @php($editable = $review->created_at->gte(now()->subDays(30)))
                <article class="py-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <a href="{{ route('providers.show', ['providerProfile' => $review->providerProfile->slug]) }}" class="font-bold hover:text-[var(--green)]">{{ $review->providerProfile->user->name }}</a>
                            <p class="mt-1 text-xs text-[var(--muted)]">Diajukan {{ $review->created_at->format('d M Y, H:i') }}</p>
                            <p class="mt-3 text-sm text-[var(--muted)]">{{ $review->comment }}</p>
                        </div>
                        <div class="text-right">
                            <span class="shrink-0 font-bold">{{ str_repeat('★', $review->rating) }}</span>
                            <p class="mt-1 text-xs font-bold {{ $review->is_published ? 'text-emerald-800' : 'text-rose-800' }}">{{ $review->is_published ? 'Dipublikasikan' : 'Disembunyikan oleh moderasi' }}</p>
                        </div>
                    </div>

                    @if ($editable)
                        <details class="mt-4 rounded-md border border-[var(--line)] bg-white p-4">
                            <summary class="cursor-pointer text-sm font-bold text-[var(--green)]">Edit ulasan</summary>
                            <form method="POST" action="{{ route('client.reviews.update', $review) }}" class="mt-4 space-y-4">
                                @csrf @method('PATCH')
                                <label class="block text-sm font-bold">Rating
                                    <select name="rating" class="field-control mt-2" required>
                                        @foreach (range(5, 1) as $rating)
                                            <option value="{{ $rating }}" @selected($review->rating === $rating)>{{ str_repeat('★', $rating) }} · {{ $rating }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="block text-sm font-bold">Ulasan
                                    <textarea name="comment" rows="3" maxlength="500" class="field-control mt-2" required>{{ $review->comment }}</textarea>
                                </label>
                                <button class="button-primary" type="submit">Simpan perubahan</button>
                            </form>
                        </details>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-[var(--muted)]">Bisa diedit atau dihapus sampai {{ $review->created_at->copy()->addDays(30)->format('d M Y, H:i') }}. Jatah ulasan berikutnya tetap dihitung dari tanggal pengajuan awal.</p>
                            <form method="POST" action="{{ route('client.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus ulasan ini? Anda baru dapat mengirim ulasan untuk provider ini kembali setelah 30 hari dari tanggal pengajuan awal.')">
                                @csrf @method('DELETE')
                                <button class="rounded-md border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700" type="submit">Hapus ulasan</button>
                            </form>
                        </div>
                    @else
                        <p class="mt-3 text-xs text-[var(--muted)]">Masa edit/hapus 30 hari telah berakhir.</p>
                    @endif
                </article>
            @empty
                <p class="py-10 text-sm text-[var(--muted)]">Anda belum menulis ulasan.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $reviews->links() }}</div>
    </section>
@endsection