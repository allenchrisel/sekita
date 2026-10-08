<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case APPROVED = 'APPROVED'; // laporan disetujui -> ulasan dihapus/disembunyikan
    case REJECTED = 'REJECTED'; // laporan ditolak -> ulasan dipertahankan
}
