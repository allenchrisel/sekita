<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewReply extends Model
{
    protected $fillable = ['review_id', 'provider_profile_id', 'reply_text'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}
