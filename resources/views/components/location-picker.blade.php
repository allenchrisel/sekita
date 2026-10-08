@props([
    'provinces',
    'selectedProvince' => '',
    'selectedRegency' => '',
    'selectedDistrict' => '',
    'required' => false,
    'compact' => false,
])

<div {{ $attributes->class('grid gap-3 sm:grid-cols-3') }} x-data="locationPicker({
    province: @js((string) $selectedProvince),
    regency: @js((string) $selectedRegency),
    district: @js((string) $selectedDistrict),
    provinces: @js($provinces->map(fn ($province) => ['code' => $province->code, 'name' => $province->name])->values()),
})" x-init="init()">
    <label class="block text-xs font-bold text-[var(--muted)]">Provinsi
        <select class="field-control mt-2" name="province_code" x-model="province" x-on:change="changeProvince()" @required($required)>
            <option value="">Pilih provinsi</option>
            <template x-for="item in provinces" :key="item.code">
                <option :value="item.code" x-text="item.name"></option>
            </template>
        </select>
    </label>
    <label class="block text-xs font-bold text-[var(--muted)]">Kabupaten / kota
        <select class="field-control mt-2" name="regency_code" x-model="regency" x-on:change="changeRegency()" x-bind:disabled="!province || loadingRegencies" @required($required)>
            <option value="" x-text="loadingRegencies ? 'Memuat kabupaten/kota...' : 'Pilih kabupaten/kota'"></option>
            <template x-for="item in regencies" :key="item.code">
                <option :value="item.code" x-text="item.name"></option>
            </template>
        </select>
    </label>
    <label class="block text-xs font-bold text-[var(--muted)]">Kecamatan
        <select class="field-control mt-2" name="district_code" x-model="district" x-bind:disabled="!regency || loadingDistricts" @required($required)>
            <option value="" x-text="loadingDistricts ? 'Memuat kecamatan...' : 'Semua kecamatan'"></option>
            <template x-for="item in districts" :key="item.code">
                <option :value="item.code" x-text="item.name"></option>
            </template>
        </select>
    </label>
    <p class="sr-only" aria-live="polite" x-text="error"></p>
</div>