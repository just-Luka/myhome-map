<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'plan', 'organization_id', 'role', 'save_limit'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function savedListings()
    {
        return $this->hasMany(SavedListing::class);
    }

    public function isPro(): bool        { return $this->plan === 'pro'; }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isCeo(): bool        { return $this->role === 'ceo'; }
    public function isEmployee(): bool     { return $this->role === 'employee'; }
    public function inOrg(): bool        { return $this->organization_id !== null; }
}
