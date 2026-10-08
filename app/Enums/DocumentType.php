<?php

namespace App\Enums;

enum DocumentType: string
{
    case KTP = 'KTP';
    case IJAZAH = 'IJAZAH';
    case SERTIFIKAT = 'SERTIFIKAT';

    public function label(): string
    {
        return match ($this) {
            self::KTP => 'KTP',
            self::IJAZAH => 'Ijazah',
            self::SERTIFIKAT => 'Sertifikat',
        };
    }
}
