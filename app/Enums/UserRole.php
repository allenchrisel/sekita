<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'CLIENT';
    case PROVIDER = 'PROVIDER';
    case ADMIN = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::CLIENT => 'Pencari Jasa',
            self::PROVIDER => 'Penyedia Jasa',
            self::ADMIN => 'Administrator',
        };
    }
}
