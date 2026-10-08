@extends('layouts.antari', ['title' => 'Edit profil jasa'])

@section('content')
    <section class="mx-auto max-w-4xl px-5 py-9 lg:px-8">
        <a href="{{ route('provider.dashboard') }}" class="text-sm font-bold text-[var(--muted)]">← Kembali ke dashboard</a>
        <div class="mt-5"><p class="text-xs font-bold uppercase text-[var(--coral)]">Profil publik</p><h1 class="mt-2 font-display text-4xl">Informasi jasa Anda</h1></div>
        <x-provider-workspace-nav active="profile" />
        <form method="POST" action="{{ route('provider.profile.update') }}" class="mt-7 space-y-6 rounded-md border border-[var(--line)] bg-white p-5 sm:p-8">
            @csrf
            @method('PATCH')
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="block text-sm font-bold">Kategori jasa
                    <select name="category_id" class="field-control mt-2" required>
                        @foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $profile->category_id) == $category->id)>{{ $category->name }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-bold">Judul keahlian
                    <input class="field-control mt-2" name="title" value="{{ old('title', $profile->title) }}" maxlength="120" required>
                </label>
            </div>
            <label class="block text-sm font-bold">Tentang dan pengalaman
                <textarea class="field-control mt-2" name="bio" rows="5" maxlength="1500">{{ old('bio', $profile->bio) }}</textarea>
            </label>
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="block text-sm font-bold">Tarif awal (Rp)
                    <input class="field-control mt-2" type="number" name="starting_price" min="0" value="{{ old('starting_price', $profile->starting_price) }}">
                </label>
                <label class="block text-sm font-bold">WhatsApp
                    <input class="field-control mt-2" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}" maxlength="20" required>
                </label>
                <label class="block text-sm font-bold">Instagram URL
                    <input class="field-control mt-2" type="url" name="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}">
                </label>
                <label class="block text-sm font-bold">Website
                    <input class="field-control mt-2" type="url" name="website_url" value="{{ old('website_url', $profile->website_url) }}">
                </label>
            </div>
            <fieldset>
                <legend class="mb-3 text-sm font-bold">Lokasi layanan</legend>
                <x-location-picker
                    :provinces="$provinces"
                    :selected-province="old('province_code', $profile->province_code)"
                    :selected-regency="old('regency_code', $profile->regency_code)"
                    :selected-district="old('district_code', $profile->district_code)"
                    :required="true"
                />
            </fieldset>
            <label class="block text-sm font-bold">Alamat / area layanan
                <input class="field-control mt-2" name="address" value="{{ old('address', $profile->address) }}" maxlength="255" placeholder="Nama jalan, gedung, atau patokan (opsional)">
            </label>
            <div class="flex justify-end border-t border-[var(--line)] pt-5"><button class="button-primary" type="submit">Simpan perubahan</button></div>
        </form>

        <section class="mt-8 rounded-md border border-[var(--line)] bg-white p-5 sm:p-8">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div><p class="text-xs font-bold uppercase text-[var(--coral)]">Bukti pekerjaan</p><h2 class="mt-2 font-display text-2xl">Foto profil & galeri</h2></div>
                <span class="text-sm font-semibold text-[var(--muted)]">{{ $profile->portfolioGalleries->count() }} / 6 foto</span>
            </div>
            <form method="POST" action="{{ route('provider.profile.portfolio.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                @csrf
                <label class="block text-sm font-bold">Pilih foto
                    <input class="field-control mt-2" type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                </label>
                <label class="block text-sm font-bold">Keterangan (opsional)
                    <input class="field-control mt-2" name="caption" maxlength="160" placeholder="Contoh: hasil renovasi dapur">
                </label>
                <button class="button-primary" type="submit" @disabled($profile->portfolioGalleries->count() >= 6)>Unggah foto</button>
            </form>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($profile->portfolioGalleries as $gallery)
                    @php($image = str_starts_with($gallery->image_url, 'http') ? $gallery->image_url : \Illuminate\Support\Facades\Storage::disk('public')->url($gallery->image_url))
                    <article class="overflow-hidden rounded-md border border-[var(--line)]">
                        <img src="{{ $image }}" alt="{{ $gallery->caption ?: 'Foto pekerjaan' }}" class="aspect-[4/3] w-full image-cover" loading="lazy">
                        <div class="flex items-center justify-between gap-3 p-3">
                            <p class="text-sm">{{ $gallery->caption ?: 'Tanpa keterangan' }}</p>
                            <form method="POST" action="{{ route('provider.profile.portfolio.destroy', $gallery) }}" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="col-span-full border-y border-[var(--line)] py-7 text-sm text-[var(--muted)]">Belum ada foto. Foto yang diunggah akan tampil di profil publik.</p>
                @endforelse
            </div>
        </section>
    </section>
@endsection