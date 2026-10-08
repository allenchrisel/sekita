@extends('layouts.antari', ['title' => 'Verifikasi dokumen'])

@section('content')
    <section class="mx-auto max-w-7xl px-5 py-9 lg:px-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-[var(--muted)]">← Kembali ke moderasi</a>
        <div class="mt-5"><p class="text-xs font-bold uppercase text-[var(--coral)]">Berkas privat</p><h1 class="mt-2 font-display text-4xl">Verifikasi dokumen</h1></div>
        <x-admin-workspace-nav active="documents" />
        <nav class="mt-7 flex gap-2 overflow-x-auto border-b border-[var(--line)] pb-3" aria-label="Filter kategori dokumen">
            <a href="{{ route('admin.documents.index') }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-bold', 'bg-[var(--green)] text-white' => ! $selectedType, 'bg-white text-[var(--muted)] hover:text-[var(--ink)]' => $selectedType])>Semua <span class="ml-1 opacity-75">{{ $documentCounts->sum() }}</span></a>
            @foreach (\App\Enums\DocumentType::cases() as $type)
                <a href="{{ route('admin.documents.index', ['type' => $type->value]) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-bold', 'bg-[var(--green)] text-white' => $selectedType === $type->value, 'bg-white text-[var(--muted)] hover:text-[var(--ink)]' => $selectedType !== $type->value])>{{ $type->label() }} <span class="ml-1 opacity-75">{{ $documentCounts[$type->value] ?? 0 }}</span></a>
            @endforeach
        </nav>
        <div class="mt-7 overflow-x-auto rounded-md border border-[var(--line)] bg-white">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-[var(--line)] bg-[#f0f4ef] text-xs uppercase text-[var(--muted)]"><tr><th class="px-5 py-4">Penyedia</th><th class="px-5 py-4">Dokumen</th><th class="px-5 py-4">Status</th><th class="px-5 py-4">Keputusan</th></tr></thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse ($documents as $document)
                        <tr class="align-top">
                            <td class="px-5 py-5"><p class="font-bold">{{ $document->user->name }}</p><p class="mt-1 text-xs text-[var(--muted)]">{{ $document->user->email }}</p><p class="mt-2 text-xs text-[var(--muted)]">Diajukan {{ $document->created_at->format('d M Y, H:i') }}</p></td>
                            <td class="px-5 py-5"><p class="font-bold">{{ $document->document_type->label() }}</p><a class="mt-2 inline-block font-bold text-[var(--green)] underline" href="{{ route('admin.documents.download', $document) }}" data-turbo="false">Buka berkas privat ↗</a></td>
                            <td class="px-5 py-5">
                                @php($statusStyle = match ($document->status->value) { 'VERIFIED' => 'bg-emerald-50 text-emerald-800', 'REJECTED' => 'bg-rose-50 text-rose-800', default => 'bg-amber-50 text-amber-900' })
                                @php($statusLabel = match ($document->status->value) { 'VERIFIED' => 'Disetujui', 'REJECTED' => 'Ditolak', default => 'Menunggu keputusan' })
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusStyle }}">{{ $statusLabel }}</span>
                                @if ($document->reviewed_at)<p class="mt-2 text-xs text-[var(--muted)]">Diputuskan {{ $document->reviewed_at->format('d M Y, H:i') }}</p>@endif
                                @if ($document->rejection_reason)<p class="mt-2 max-w-52 text-xs text-[var(--muted)]">{{ $document->rejection_reason }}</p>@endif
                            </td>
                            <td class="px-5 py-5">
                                <div class="flex flex-col gap-5">
                                    <form method="POST" action="{{ route('admin.documents.update', $document) }}">
                                        @csrf @method('PATCH')<input type="hidden" name="status" value="VERIFIED">
                                        <button class="rounded-md bg-[var(--green)] px-3 py-2 text-xs font-bold text-white" type="submit">Setujui & terbitkan badge</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.documents.update', $document) }}" class="flex flex-col gap-3">
                                        @csrf @method('PATCH')<input type="hidden" name="status" value="REJECTED">
                                        <input class="field-control text-xs" name="rejection_reason" placeholder="Alasan penolakan" maxlength="1000" required>
                                        <button class="self-start rounded-md border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700" type="submit">Tolak dokumen</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-[var(--muted)]">Belum ada dokumen yang dikirim.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $documents->links() }}</div>
    </section>
@endsection