<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = ['provider_profile_id', 'client_id', 'rating', 'comment', 'is_published'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function reply(): HasOne
    {
        return $this->hasOne(ReviewReply::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(ReviewDispute::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
