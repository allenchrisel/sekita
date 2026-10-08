<?php

namespace App\Models;

use App\Models\Regency;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'name'];

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class, 'province_code', 'code');
    }
}