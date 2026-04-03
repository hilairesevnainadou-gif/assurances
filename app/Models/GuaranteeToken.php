<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuaranteeToken extends Model
{
    use HasFactory;

    protected $table = 'guarantee_tokens';

    protected $fillable = [
        'funding_request_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relations
    public function fundingRequest(): BelongsTo
    {
        return $this->belongsTo(FundingRequest::class);
    }

    // Accesseurs
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at < now();
    }

    public function getIsUsedAttribute(): bool
    {
        return !is_null($this->used_at);
    }

    public function getIsValidAttribute(): bool
    {
        return !$this->isExpired && !$this->isUsed;
    }
}
