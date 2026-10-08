<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'cover_image'];

    public function providerProfiles(): HasMany
    {
        return $this->hasMany(ProviderProfile::class);
    }
}
