<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationDocument extends Model
{
    protected $fillable = [
        'user_id', 'document_type', 'private_file_url', 'status', 'rejection_reason',
        'reviewed_at',
    ];

    // Path file privat tidak pernah diekspos ke JSON/API.
    protected $hidden = ['private_file_url'];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'status' => VerificationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', VerificationStatus::PENDING);
    }
}
