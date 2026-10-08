# Tambahan config/filesystems.php

Di dalam array `'disks' => [ ... ]`, tambahkan disk privat berikut:

```php
'private_documents' => [
    'driver' => 'local',
    'root' => storage_path('app/private_documents'),
    'visibility' => 'private',
    'serve' => false,   // tidak pernah dilayani lewat URL publik
    'throw' => true,
],
```

Catatan: pada Laravel 11, disk `local` bawaan mengarah ke `storage/app/private`.
Disk khusus ini menjaga dokumen KTP/Ijazah tepat di `storage/app/private_documents`
sesuai PRD, di luar `public/`.
