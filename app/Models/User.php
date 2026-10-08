<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'is_verified', 'email_verified_at', 'last_online_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_online_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_verified' => 'boolean',
        ];
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'is_verified' => true,
        ])->save();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isProvider(): bool
    {
        return $this->role === UserRole::PROVIDER;
    }

    public function isClient(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function verificationDocuments(): HasMany
    {
        return $this->hasMany(VerificationDocument::class);
    }

    /** Ulasan yang ditulis user ini sebagai client. */
    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    public function reviewDisputes(): HasMany
    {
        return $this->hasMany(ReviewDispute::class, 'reporter_id');
    }
}
