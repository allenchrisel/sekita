<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-md border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-900">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <p class="text-xs font-bold uppercase text-[var(--coral)]">Selamat datang kembali</p>
    <h1 class="mb-6 mt-2 font-display text-3xl">Masuk ke SeKita</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label class="block text-sm font-bold" for="email">Email
            <input id="email" class="field-control mt-2" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </label>
        <label class="mt-4 block text-sm font-bold" for="password">Kata sandi
            <input id="password" class="field-control mt-2" type="password" name="password" required autocomplete="current-password">
        </label>
        <div class="mt-4 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-[var(--muted)]">
                <input id="remember_me" type="checkbox" class="rounded border-[var(--line)] text-[var(--green)] focus:ring-[var(--green)]" name="remember">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-[var(--green)] underline" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>
        <button class="button-primary mt-6 w-full" type="submit">Masuk</button>
    </form>
    <p class="mt-5 text-center text-sm text-[var(--muted)]">Belum punya akun? <a class="font-bold text-[var(--green)] underline" href="{{ route('register') }}">Daftar</a></p>
</x-guest-layout>
