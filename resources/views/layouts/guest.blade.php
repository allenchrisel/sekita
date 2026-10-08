<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SeKita') }} · Sekitar Kita</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[var(--ink)] antialiased">
        <div class="min-h-screen bg-[var(--paper)] px-5 py-10 sm:grid sm:place-items-center">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-7 flex items-center justify-center gap-2 text-xl font-bold"><span class="grid h-9 w-9 place-items-center rounded-md bg-[var(--lime)] text-sm">S</span> SeKita</a>
            <div class="rounded-md border border-[var(--line)] bg-white px-6 py-7 shadow-sm sm:px-8">
                {{ $slot }}
            </div>
            <a href="{{ route('home') }}" class="mt-5 block text-center text-sm font-semibold text-[var(--muted)]">Kembali ke marketplace</a>
            </div>
        </div>
    </body>
</html>
