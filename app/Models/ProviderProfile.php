<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProviderProfile extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'province_code', 'regency_code', 'district_code', 'slug', 'title', 'bio', 'starting_price',
        'whatsapp_number', 'instagram_url', 'website_url',
        'address', 'latitude', 'longitude',
        'id_verified_badge', 'degree_badge', 'avg_rating', 'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'id_verified_badge' => 'boolean',
            'degree_badge' => 'boolean',
            'avg_rating' => 'float',
            'total_reviews' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProviderProfile $profile): void {
            if (blank($profile->slug)) {
                $profile->slug = static::uniqueSlugForName($profile->user?->name ?? 'provider');
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlugForName(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'provider';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '<>', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_code', 'code');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function portfolioGalleries(): HasMany
    {
        return $this->hasMany(PortfolioGallery::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function publishedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_published', true);
    }

    public function reviewReplies(): HasMany
    {
        return $this->hasMany(ReviewReply::class);
    }

    /** Nomor WhatsApp dinormalisasi ke format internasional (62xxx) tanpa simbol. */
    protected function whatsappE164(): Attribute
    {
        return Attribute::get(function () {
            $digits = preg_replace('/\D+/', '', $this->whatsapp_number ?? '');

            return match (true) {
                str_starts_with($digits, '62') => $digits,
                str_starts_with($digits, '0') => '62'.substr($digits, 1),
                default => $digits,
            };
        });
    }

    /** Tautan wa.me dengan draf pesan otomatis. */
    public function whatsappUrl(?string $message = null): string
    {
        $message ??= "Halo {$this->user->name}, saya menemukan profil Anda di SeKita dan tertarik dengan jasa {$this->title}.";

        return 'https://wa.me/'.$this->whatsapp_e164.'?text='.rawurlencode($message);
    }

    /** Hitung ulang rating rata-rata & total ulasan dari ulasan yang dipublikasikan. */
    public function refreshRatingStats(): void
    {
        $stats = $this->publishedReviews()
            ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as average')
            ->first();

        $this->update([
            'total_reviews' => (int) $stats->total,
            'avg_rating' => round((float) $stats->average, 2),
        ]);
    }
}
