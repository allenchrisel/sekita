# SeKita

SeKita (Sekitar Kita) adalah marketplace jasa lokal berbasis Laravel 12, Blade, Tailwind CSS 3.4, Alpine.js, dan Laravel Breeze. Laravel 12 dipakai karena Composer memblokir rilis Laravel 11 yang tersedia akibat security advisories; proyek tetap mendukung PHP 8.2 dan struktur bootstrap Laravel modern.

## Menjalankan Lokal

Prasyarat: PHP 8.2+, Composer 2.6+, Node.js 18+ (Node 20 direkomendasikan), serta ekstensi PHP GD dan PDO. SQLite aktif sebagai database lokal awal; aplikasi juga mendukung MySQL 8+ dan PostgreSQL 15+.

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite | Out-Null }
php artisan migrate --seed
npm install
npm run build
php artisan storage:link
php artisan serve --no-reload
```

Buka `http://127.0.0.1:8000`. Untuk development frontend dengan hot reload, jalankan `npm run dev` di terminal kedua.

### MySQL atau PostgreSQL

Ubah `.env` menjadi salah satu konfigurasi berikut, buat databasenya terlebih dahulu, lalu jalankan migrasi dan seeder.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antari
DB_USERNAME=root
DB_PASSWORD=
```

Untuk PostgreSQL, pakai `DB_CONNECTION=pgsql`, port `5432`, dan kredensial PostgreSQL lokal Anda.

## Deploy ke Railway

Railway membaca `Dockerfile` di root repository. Image membangun aset Vite dan menyediakan ekstensi PHP untuk SQLite, MySQL, dan pemrosesan gambar.

1. Push perubahan ke GitHub, lalu buat project Railway dengan **Deploy from GitHub repo** dan pilih repository ini.
2. Di project yang sama, pilih **+ New → Database → MySQL**. Tunggu sampai service MySQL selesai dibuat.
3. Di service aplikasi, tambahkan variables berikut:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<hasil php artisan key:generate --show>
   APP_URL=https://<domain-Railway>
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   ```

   Jika nama service MySQL bukan `MySQL`, ganti bagian sebelum titik pada setiap reference agar sama persis dengan nama service. Hapus `DB_URL` jika sebelumnya sempat ditambahkan karena konfigurasi koneksi ini memakai variabel host dan kredensial secara terpisah. Buat `APP_KEY` sekali secara lokal dengan `php artisan key:generate --show`, lalu simpan nilainya hanya di Railway Variables; jangan commit `.env` atau key ke GitHub. Gunakan domain publik yang dibuat Railway untuk `APP_URL`.
4. Deploy aplikasi. Startup container menjalankan `php artisan migrate --force`; Railway mengarahkan traffic ke port yang diberikan melalui `PORT`.
   Pastikan `APP_URL` memakai domain publik HTTPS. Laravel mempercayai proxy Railway agar URL aset dan tautan yang dibuat aplikasi juga menggunakan HTTPS.
5. Untuk mengisi data awal yang tidak membuat akun demo dengan password publik, buka shell pada service aplikasi setelah deploy lalu jalankan:

   ```sh
   php artisan db:seed --class=RegionSeeder --force
   php artisan db:seed --class=CategorySeeder --force
   ```

   Jangan jalankan `DatabaseSeeder` di deployment publik karena ia membuat akun demo dengan password yang sudah diketahui dan memasukkan data contoh.

Railway mengganti filesystem container saat redeploy. Database MySQL akan mempertahankan data aplikasi, tetapi gambar portfolio dan dokumen yang diunggah saat ini disimpan di filesystem lokal container; file tersebut tidak dijamin bertahan. Untuk penggunaan berkelanjutan, pindahkan file unggahan ke object storage (misalnya S3-compatible), atau pasang volume Railway pada lokasi storage setelah memahami biaya dan batas paketnya. Per [halaman harga Railway](https://railway.com/pricing) saat panduan ini diperbarui, paket Free mencakup kredit pemakaian bulanan terbatas ($1); aplikasi dan MySQL yang berjalan terus-menerus bisa melebihi kredit itu. Harga dan batas paket dapat berubah, jadi cek sebelum deploy bila harus benar-benar gratis.

Email verifikasi dan reset password juga perlu konfigurasi SMTP melalui Railway Variables. Tanpa SMTP, mailer default `log` hanya mencatat pesan ke log aplikasi, bukan mengirimkannya ke pengguna.

## Akun Demo

Semua akun demo memakai kata sandi `password123`.

| Role | Email | Area uji |
| --- | --- | --- |
| Admin | `admin@antari.id` | Verifikasi dokumen privat dan moderasi dispute |
| Provider | `ahmad.provider@antari.id` | Profil, WebP portfolio, dokumen, balasan ulasan |
| Client terverifikasi | `budi.client@antari.id` | Pencarian, detail, WhatsApp, ulasan |

Seeder juga membuat 100 provider tambahan: email `provider.demo.001@sekita.id` sampai `provider.demo.100@sekita.id`, semuanya memakai kata sandi `password123`. Profil demo tersebar di seluruh kategori dan lokasi yang tersedia.
Setiap provider demo memiliki satu ulasan contoh yang ditulis oleh akun client demo Budi Santoso (`budi.client@antari.id`). Ulasan diberi penanda “Contoh ulasan demo” dan bukan testimoni pengguna sungguhan.

Registrasi publik menyediakan role client atau provider. Provider mendapat profil awal dan harus melengkapi nomor WhatsApp; admin hanya dapat dibuat melalui seed atau proses internal.

Email verifikasi lokal dicatat oleh mailer `log` di `storage/logs/laravel.log`. Client perlu membuka tautan verifikasi sebelum dapat mengirim ulasan.

## Fitur

- Form pencarian memperbarui Turbo Frame daftar provider tanpa reload seluruh halaman dan menggulir halus ke hasil; navigasi serta pagination tetap server-side dengan 12 hasil per halaman.
- Pencarian provider dengan pagination database (12 hasil per halaman), filter kata kunci/kategori, dan wilayah provinsi → kabupaten/kota → kecamatan.
- Profil publik dengan badge verifikasi, galeri, rating, balasan provider, serta tautan WhatsApp/telepon.
- Rating 1–5, ulasan maksimal 500 karakter, filter profanity, penghapusan URL/nomor telepon, dan satu ulasan per provider per 30 hari.
- Dashboard provider untuk memperbarui profil, mengunggah maksimal enam gambar yang diperkecil dan dikonversi menjadi WebP, mengirim dokumen privat, membalas dan melaporkan ulasan.
- Dashboard admin untuk mengunduh dokumen melalui controller terautentikasi, menyetujui/menolak verifikasi, serta menyembunyikan atau mempertahankan ulasan berdasarkan hasil pemeriksaan dispute.
- URL profil publik provider memakai slug unik dari nama akun (`/providers/nama-provider`).
- Panel admin menyediakan pencarian dan pagination daftar client/provider serta penghapusan akun dengan konfirmasi; relasi database dan file portfolio/dokumen privat dibersihkan.
- Pesan gagal login dan navigasi pagination tersedia dalam Bahasa Indonesia.

Dokumen verifikasi disimpan pada `storage/app/private_documents`, tidak melalui public storage. Foto portfolio disimpan pada `storage/app/public/portfolios` dan membutuhkan `php artisan storage:link` agar dapat ditampilkan.

Data wilayah offline berada di `database/data/indonesia-regions.json` dan memuat 38 provinsi, 514 kabupaten/kota, serta 7.285 kecamatan. Snapshot diunduh dari [wilayah.id](https://wilayah.id/api/provinces.json) (metadata sumber diperbarui 4 Juli 2025); seeder tidak membutuhkan koneksi internet. Form hanya memuat 38 provinsi awal, lalu mengambil kabupaten/kota dan kecamatan yang sesuai dari endpoint lokal.

## Pemeriksaan

```powershell
php artisan test
npm run build
composer audit
```