<x-guest-layout>
    <p class="text-xs font-bold uppercase text-[var(--coral)]">Mulai terhubung</p>
    <h1 class="mb-6 mt-2 font-display text-3xl">Buat akun SeKita</h1>
    <form method="POST" action="{{ route('register') }}" x-data="{ accountType: '{{ old('account_type', 'CLIENT') }}' }">
        @csrf
        <label class="block text-sm font-bold" for="name">Nama lengkap
            <input id="name" class="field-control mt-2" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>
        <label class="mt-4 block text-sm font-bold" for="email">Email
            <input id="email" class="field-control mt-2" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>
        <label class="mt-4 block text-sm font-bold" for="phone">Nomor telepon / WhatsApp
            <input id="phone" class="field-control mt-2" type="tel" name="phone" value="{{ old('phone') }}" maxlength="20" x-bind:required="accountType === 'PROVIDER'" autocomplete="tel">
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </label>
        <label class="mt-4 block text-sm font-bold" for="account_type">Saya ingin bergabung sebagai
            <select id="account_type" class="field-control mt-2" name="account_type" x-model="accountType">
                <option value="CLIENT">Pencari jasa</option>
                <option value="PROVIDER">Penyedia jasa</option>
            </select>
        </label>
        <label class="mt-4 block text-sm font-bold" for="password">Kata sandi
            <input id="password" class="field-control mt-2" type="password" name="password" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>
        <label class="mt-4 block text-sm font-bold" for="password_confirmation">Ulangi kata sandi
            <input id="password_confirmation" class="field-control mt-2" type="password" name="password_confirmation" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </label>
        <button class="button-primary mt-6 w-full" type="submit">Buat akun</button>
    </form>
    <p class="mt-5 text-center text-sm text-[var(--muted)]">Sudah punya akun? <a class="font-bold text-[var(--green)] underline" href="{{ route('login') }}">Masuk</a></p>
</x-guest-layout>
