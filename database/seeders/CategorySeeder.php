<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Guru Les / Privat', 'Guru privat, tutor, dan pengajar mata pelajaran maupun keterampilan.', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1000&q=82'],
            ['Tukang / Renovasi', 'Tukang bangunan, cat, keramik, dan renovasi rumah.', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1000&q=82'],
            ['Teknisi Listrik / AC', 'Instalasi listrik, servis AC, dan perbaikan elektronik.', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=82'],
            ['Kebersihan / Laundry', 'Jasa bersih rumah, kantor, dan laundry harian.', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1000&q=82'],
            ['Rumah Tangga / Pengasuhan', 'Asisten rumah tangga, babysitter, nanny, caregiver lansia, dan pendamping harian.', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1000&q=82'],
            ['Kuliner / Katering', 'Chef atau koki panggilan, katering, meal prep, baker, dan masakan rumahan.', 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1000&q=82'],
            ['Desain / Kreatif', 'Desainer grafis, fotografer, videografer, dan penulis.', 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?auto=format&fit=crop&w=1000&q=82'],
            ['Otomotif', 'Bengkel panggilan, servis motor, dan mobil.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1000&q=82'],
            ['Kesehatan / Kebugaran', 'Terapis pijat, personal trainer, dan perawat panggilan.', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=82'],
            ['Konsultan / Profesional', 'Konsultan hukum, pajak, akuntansi, dan bisnis lokal.', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1000&q=82'],
        ];

        foreach ($categories as [$name, $description, $coverImage]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description, 'cover_image' => $coverImage],
            );
        }
    }
}
