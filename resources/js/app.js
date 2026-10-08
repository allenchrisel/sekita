import './bootstrap';

import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';

Turbo.config.drive.progressBarDelay = 120;
window.Turbo = Turbo;
window.Alpine = Alpine;

Alpine.data('locationPicker', (state) => ({
	province: state.province,
	regency: state.regency,
	district: state.district,
	provinces: state.provinces,
	regencies: [],
	districts: [],
	loadingRegencies: false,
	loadingDistricts: false,
	error: '',

	async init() {
		if (this.province) {
			await this.loadRegencies(true);
			if (this.regency) await this.loadDistricts(true);
		}
	},

	async loadRegencies(keepSelection = false) {
		this.loadingRegencies = true;
		this.error = '';

		try {
			const response = await fetch(`/locations/${encodeURIComponent(this.province)}/regencies`);
			if (!response.ok) throw new Error('Kabupaten/kota gagal dimuat.');
			this.regencies = (await response.json()).data;
			if (!keepSelection || !this.regencies.some((item) => item.code === this.regency)) this.regency = '';
		} catch (error) {
			this.error = error.message;
		} finally {
			this.loadingRegencies = false;
		}
	},

	async loadDistricts(keepSelection = false) {
		this.loadingDistricts = true;
		this.error = '';

		try {
			const response = await fetch(`/locations/${encodeURIComponent(this.regency)}/districts`);
			if (!response.ok) throw new Error('Kecamatan gagal dimuat.');
			this.districts = (await response.json()).data;
			if (!keepSelection || !this.districts.some((item) => item.code === this.district)) this.district = '';
		} catch (error) {
			this.error = error.message;
		} finally {
			this.loadingDistricts = false;
		}
	},

	async changeProvince() {
		this.regency = '';
		this.district = '';
		this.regencies = [];
		this.districts = [];
		if (this.province) await this.loadRegencies();
	},

	async changeRegency() {
		this.district = '';
		this.districts = [];
		if (this.regency) await this.loadDistricts();
	},
}));

Alpine.start();

document.addEventListener('turbo:before-cache', () => Alpine.destroyTree(document.body));
document.addEventListener('turbo:render', () => Alpine.initTree(document.body));
document.addEventListener('turbo:frame-load', (event) => {
	if (event.target.id === 'provider-results') {
		document.querySelector('#providers')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}
});
