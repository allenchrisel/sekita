<?php

namespace App\Models;

use App\Enums\DisputeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewDispute extends Model
{
    protected $fillable = ['review_id', 'reporter_id', 'reason', 'evidence_details', 'status', 'resolved_at'];

    protected function casts(): array
    {
        return [
            'status' => DisputeStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', DisputeStatus::UNDER_REVIEW);
    }
}
