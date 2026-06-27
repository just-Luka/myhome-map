<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'logo', 'user_limit', 'show_team_saves', 'show_team_prices', 'save_limit'];

    protected $casts = ['show_team_saves' => 'boolean', 'show_team_prices' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invites()
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    public function savedListings()
    {
        return $this->hasMany(SavedListing::class);
    }

    public function memberCount(): int
    {
        return $this->users()->count();
    }

    public function canAddMember(): bool
    {
        return $this->memberCount() < $this->user_limit;
    }
}
