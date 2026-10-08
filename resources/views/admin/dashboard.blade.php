@extends('layouts.antari', ['title' => 'Moderasi SeKita'])

@section('content')
    <section class="mx-auto max-w-7xl px-5 py-9 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><p class="text-xs font-bold uppercase text-[var(--coral)]">Pusat moderasi</p><h1 class="mt-2 font-display text-4xl">Selamat datang, {{ auth()->user()->name }}.</h1></div>
            <a href="{{ route('home') }}" class="button-secondary">Buka SeKita ↗</a>
        </div>
        <x-admin-workspace-nav active="dashboard" />

        <div class="mt-7 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('admin.documents.index') }}" class="flex items-center justify-between rounded-md border border-[var(--line)] bg-white p-5 hover:border-[var(--green)]">
                <div><p class="text-sm text-[var(--muted)]">Dokumen menunggu</p><p class="mt-1 font-display text-4xl">{{ $documentCount }}</p></div>
                <span class="grid h-12 w-12 place-items-center rounded-md bg-lime-100 text-xl">▤</span>
            </a>
            <a href="{{ route('admin.disputes.index') }}" class="flex items-center justify-between rounded-md border border-[var(--line)] bg-white p-5 hover:border-[var(--green)]">
                <div><p class="text-sm text-[var(--muted)]">Laporan dalam antrean</p><p class="mt-1 font-display text-4xl">{{ $disputeCount }}</p></div>
                <span class="grid h-12 w-12 place-items-center rounded-md bg-rose-100 text-xl">⚑</span>
            </a>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            <section>
                <div class="flex items-end justify-between"><div><p class="text-xs font-bold uppercase text-[var(--coral)]">Identitas</p><h2 class="mt-2 font-display text-2xl">Dokumen terbaru</h2></div><a href="{{ route('admin.documents.index') }}" class="text-sm font-bold text-[var(--green)]">Semua →</a></div>
                <div class="mt-4 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-4">
                    @forelse ($pendingDocuments as $document)
                        <a href="{{ route('admin.documents.index') }}" class="flex items-center justify-between gap-3 py-4">
                            <div><p class="font-bold">{{ $document->user->name }}</p><p class="mt-1 text-xs text-[var(--muted)]">{{ $document->document_type->label() }} · {{ $document->created_at->diffForHumans() }}</p></div>
                            <span class="text-xs font-bold text-amber-800">Menunggu</span>
                        </a>
                    @empty
                        <p class="py-5 text-sm text-[var(--muted)]">Tidak ada dokumen tertunda.</p>
                    @endforelse
                </div>
            </section>
            <section>
                <div class="flex items-end justify-between"><div><p class="text-xs font-bold uppercase text-[var(--coral)]">Kepercayaan</p><h2 class="mt-2 font-display text-2xl">Laporan ulasan terbaru</h2></div><a href="{{ route('admin.disputes.index') }}" class="text-sm font-bold text-[var(--green)]">Semua →</a></div>
                <div class="mt-4 divide-y divide-[var(--line)] border-y border-[var(--line)] bg-white px-4">
                    @forelse ($pendingDisputes as $dispute)
                        <a href="{{ route('admin.disputes.index') }}" class="block py-4">
                            <p class="font-bold">{{ $dispute->review->providerProfile->user->name }}</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">{{ $dispute->reason }} · dilaporkan {{ $dispute->reporter->name }}</p>
                        </a>
                    @empty
                        <p class="py-5 text-sm text-[var(--muted)]">Tidak ada laporan tertunda.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection