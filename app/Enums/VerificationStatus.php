<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';
}
