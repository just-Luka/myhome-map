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
}
