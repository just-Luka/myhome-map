<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedListing extends Model
{
    protected $fillable = ['user_id', 'organization_id', 'listing_id', 'listing_snapshot', 'my_price', 'note', 'link_myhome', 'link_ss', 'saved_date'];

    protected $casts = ['listing_snapshot' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function myhomePostId(): ?string
    {
        if (! $this->link_myhome) return null;
        preg_match('/\/pr\/(\d+)/i', $this->link_myhome, $m);
        return $m[1] ?? null;
    }

    public function ssPostId(): ?string
    {
        if (! $this->link_ss) return null;
        preg_match('/(\d+)$/', rtrim($this->link_ss, '/'), $m);
        return $m[1] ?? null;
    }

    public function discountPercent(): ?int
    {
        $orig = (float) ($this->listing_snapshot['price'] ?? 0);
        $mine = (float) ($this->my_price ?? 0);
        if (! $mine || ! $orig || $orig <= $mine) return null;
        return (int) round(($orig - $mine) / $orig * 100);
    }
}
