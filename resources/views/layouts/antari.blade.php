<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SeKita' }} | SeKita · Sekitar Kita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-sans antialiased">
    <div class="site-shell min-h-screen">
        <header class="border-b border-[var(--line)] bg-white">
            <div class="relative mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold tracking-tight text-[var(--ink)]">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-[var(--lime)] text-sm font-black">S</span>
                    SeKita
                </a>
                <nav class="hidden items-center gap-7 text-sm font-semibold text-[var(--muted)] md:flex">
                    @auth
                        @if (auth()->user()->isProvider())
                            <a class="hover:text-[var(--ink)]" href="{{ route('provider.dashboard') }}">Dashboard</a>
                        @elseif (auth()->user()->isAdmin())
                            <a class="hover:text-[var(--ink)]" href="{{ route('admin.dashboard') }}">Moderasi</a>
                        @else
                            <a class="hover:text-[var(--ink)]" href="{{ route('client.dashboard') }}">Ulasan saya</a>
                        @endif
                    @endauth
                </nav>
                <div class="hidden items-center gap-3 md:flex">
                    @guest
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-bold text-[var(--ink)]">Masuk</a>
                        <a href="{{ route('register') }}" class="button-primary text-sm">Buat akun</a>
                    @else
                        <span class="max-w-40 truncate text-sm font-semibold">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button-secondary text-sm" type="submit">Keluar</button>
                        </form>
                    @endguest
                </div>
                <div class="md:hidden" x-data="{ open: false }">
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-md border border-[var(--line)]" x-on:click="open = !open" aria-label="Buka menu">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
                    </button>
                    <div x-cloak x-show="open" x-transition class="absolute left-0 right-0 z-30 border-b border-[var(--line)] bg-white px-5 py-4 shadow-lg">
                        @guest
                            <a class="block py-2 font-semibold" href="{{ route('login') }}">Masuk</a>
                            <a class="block py-2 font-semibold" href="{{ route('register') }}">Buat akun</a>
                        @else
                            <a class="block py-2 font-semibold" href="{{ route('dashboard') }}">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="py-2 font-semibold" type="submit">Keluar</button></form>
                        @endguest
                    </div>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="mx-auto mt-5 max-w-7xl px-5 lg:px-8">
                <div role="status" class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">{{ session('status') }}</div>
            </div>
        @endif

        <main>{{ $slot ?? '' }}@yield('content')</main>

        <footer class="mt-20 border-t border-[var(--line)] bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-[var(--muted)] sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <span class="font-bold text-[var(--ink)]">SeKita <span class="font-normal">· Sekitar Kita.</span></span>
                <span>© {{ now()->year }} SeKita</span>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>