@extends('layouts.antari', ['title' => 'Moderasi ulasan'])

@section('content')
    <section class="mx-auto max-w-6xl px-5 py-9 lg:px-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-[var(--muted)]">← Kembali ke moderasi</a>
        <div class="mt-5"><p class="text-xs font-bold uppercase text-[var(--coral)]">Laporan komunitas</p><h1 class="mt-2 font-display text-4xl">Moderasi ulasan</h1></div>
        <x-admin-workspace-nav active="disputes" />
        <nav class="mt-7 flex gap-2 overflow-x-auto border-b border-[var(--line)] pb-3" aria-label="Filter status laporan">
            <a href="{{ route('admin.disputes.index') }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-bold', 'bg-[var(--green)] text-white' => ! $selectedStatus, 'bg-white text-[var(--muted)] hover:text-[var(--ink)]' => $selectedStatus])>Semua <span class="ml-1 opacity-75">{{ $disputeCounts->sum() }}</span></a>
            @foreach (\App\Enums\DisputeStatus::cases() as $status)
                @php($tabLabel = match ($status) { \App\Enums\DisputeStatus::UNDER_REVIEW => 'Menunggu', \App\Enums\DisputeStatus::APPROVED => 'Disetujui · disembunyikan', \App\Enums\DisputeStatus::REJECTED => 'Ditolak · dipertahankan' })
                <a href="{{ route('admin.disputes.index', ['status' => $status->value]) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-bold', 'bg-[var(--green)] text-white' => $selectedStatus === $status->value, 'bg-white text-[var(--muted)] hover:text-[var(--ink)]' => $selectedStatus !== $status->value])>{{ $tabLabel }} <span class="ml-1 opacity-75">{{ $disputeCounts[$status->value] ?? 0 }}</span></a>
            @endforeach
        </nav>
        <div class="mt-7 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-5 sm:px-7">
            @forelse ($disputes as $dispute)
                <article class="grid gap-5 py-6 lg:grid-cols-[1fr_auto]">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-bold">{{ $dispute->review->providerProfile->user->name }}</h2>
                            @php($decisionStyle = match ($dispute->status->value) { 'APPROVED' => 'bg-emerald-50 text-emerald-800', 'REJECTED' => 'bg-rose-50 text-rose-800', default => 'bg-amber-50 text-amber-900' })
                            @php($decisionLabel = match ($dispute->status->value) { 'APPROVED' => 'Disetujui · review disembunyikan', 'REJECTED' => 'Ditolak · review dipertahankan', default => 'Menunggu keputusan' })
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $decisionStyle }}">{{ $decisionLabel }}</span>
                        </div>
                        <p class="mt-2 text-xs text-[var(--muted)]">Diajukan {{ $dispute->created_at->format('d M Y, H:i') }} · Dilaporkan oleh {{ $dispute->reporter->name }}</p>
                        @if ($dispute->resolved_at)<p class="mt-1 text-xs font-semibold text-[var(--muted)]">Keputusan admin {{ $dispute->resolved_at->format('d M Y, H:i') }}</p>@endif
                        <p class="mt-4 text-sm font-bold">Alasan: {{ $dispute->reason }}</p>
                        @if ($dispute->evidence_details)<p class="mt-2 text-sm leading-6 text-[var(--muted)]">{{ $dispute->evidence_details }}</p>@endif
                        <blockquote class="mt-4 border-l-2 border-[var(--line)] pl-4 text-sm leading-6">“{{ $dispute->review->comment }}” <span class="font-bold">· ★ {{ $dispute->review->rating }} · {{ $dispute->review->client->name }}</span></blockquote>
                    </div>
                    @if ($dispute->status->value === 'UNDER_REVIEW')
                    <div class="flex flex-wrap items-start gap-2 lg:flex-col">
                        <form method="POST" action="{{ route('admin.disputes.update', $dispute) }}">
                            @csrf @method('PATCH')<input type="hidden" name="status" value="APPROVED">
                            <button class="rounded-md bg-rose-700 px-4 py-2.5 text-sm font-bold text-white" type="submit">Setujui laporan · sembunyikan</button>
                        </form>
                        <form method="POST" action="{{ route('admin.disputes.update', $dispute) }}">
                            @csrf @method('PATCH')<input type="hidden" name="status" value="REJECTED">
                            <button class="rounded-md border border-[var(--line)] bg-white px-4 py-2.5 text-sm font-bold" type="submit">Tolak laporan · pertahankan</button>
                        </form>
                    </div>
                    @endif
                </article>
            @empty
                <p class="py-10 text-sm text-[var(--muted)]">Belum ada laporan ulasan.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $disputes->links() }}</div>
    </section>
@endsection