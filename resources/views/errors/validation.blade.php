@extends('layouts.antari')

@section('content')
    <section class="mx-auto max-w-2xl px-5 py-16 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-wide text-[var(--coral)]">Permintaan belum dapat diproses</p>
        <h1 class="mt-3 font-display text-4xl">Periksa kembali isian Anda.</h1>
        <ul class="mt-6 list-disc space-y-2 pl-5 text-sm text-[var(--muted)]">
            @foreach ($messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
        <a href="{{ url()->previous() }}" class="button-primary mt-8">Kembali ke halaman sebelumnya</a>
    </section>
@endsection