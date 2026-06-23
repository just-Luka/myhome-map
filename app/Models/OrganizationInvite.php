<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInvite extends Model
{
    protected $fillable = ['organization_id', 'token', 'role', 'used_at', 'expires_at'];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }
}
