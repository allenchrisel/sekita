<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioGallery extends Model
{
    public const MAX_PHOTOS_PER_PROVIDER = 6;

    protected $fillable = ['provider_profile_id', 'image_url', 'caption', 'sort_order'];

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}
