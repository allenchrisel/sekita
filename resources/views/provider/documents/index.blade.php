@extends('layouts.antari', ['title' => 'Dokumen verifikasi'])

@section('content')
    <section class="mx-auto max-w-5xl px-5 py-9 lg:px-8">
        <a href="{{ route('provider.dashboard') }}" class="text-sm font-bold text-[var(--muted)]">← Kembali ke dashboard</a>
        <div class="mt-5"><p class="text-xs font-bold uppercase text-[var(--coral)]">Area privat</p><h1 class="mt-2 font-display text-4xl">Dokumen verifikasi</h1></div>
        <x-provider-workspace-nav active="documents" />
        <form method="POST" action="{{ route('provider.documents.store') }}" enctype="multipart/form-data" class="mt-7 grid gap-4 rounded-md border border-[var(--line)] bg-white p-5 md:grid-cols-[.7fr_1fr_auto] md:items-end">
            @csrf
            <label class="block text-sm font-bold">Jenis dokumen
                <select name="document_type" class="field-control mt-2" required>
                    <option value="KTP">KTP</option><option value="IJAZAH">Ijazah</option><option value="SERTIFIKAT">Sertifikat</option>
                </select>
            </label>
            <label class="block text-sm font-bold">Berkas (JPG, PNG, PDF · maks. 5 MB)
                <input class="field-control mt-2" type="file" name="document" accept="image/jpeg,image/png,application/pdf" required>
            </label>
            <button class="button-primary" type="submit">Kirim untuk verifikasi</button>
        </form>
        <div class="mt-8 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-5">
            @forelse ($documents as $document)
                <article class="flex flex-wrap items-center justify-between gap-4 py-5">
                    <div><p class="font-bold">{{ $document->document_type->label() }}</p><p class="mt-1 text-sm text-[var(--muted)]">Diajukan {{ $document->created_at->format('d M Y, H:i') }}</p>
                        @if ($document->reviewed_at)<p class="mt-1 text-xs text-[var(--muted)]">Diperiksa admin {{ $document->reviewed_at->format('d M Y, H:i') }}</p>@endif
                        @if ($document->rejection_reason)<p class="mt-2 text-sm text-rose-700">{{ $document->rejection_reason }}</p>@endif
                    </div>
                    @php($statusStyle = match ($document->status->value) { 'VERIFIED' => 'bg-emerald-50 text-emerald-800', 'REJECTED' => 'bg-rose-50 text-rose-800', default => 'bg-amber-50 text-amber-900' })
                    <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $statusStyle }}">{{ $document->status->value === 'VERIFIED' ? 'Terverifikasi' : ($document->status->value === 'REJECTED' ? 'Perlu dikirim ulang' : 'Menunggu pemeriksaan') }}</span>
                </article>
            @empty
                <p class="py-8 text-sm text-[var(--muted)]">Belum ada dokumen dikirim.</p>
            @endforelse
        </div>
    </section>
@endsection