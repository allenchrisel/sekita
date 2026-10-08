<?php

namespace App\Models;

use App\Models\Regency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'regency_code', 'name'];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_code', 'code');
    }
}