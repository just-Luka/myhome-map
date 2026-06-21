<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'listing_id',
        'title',
        'price',
        'currency',
        'lat',
        'lng',
        'geocoded',
        'newly_built',
        'address',
        'area',
        'rooms',
        'bedrooms',
        'rent_type',
        'district_id',
        'district_name',
        'poster_type',
        'listed_at',
        'url',
    ];

    protected $casts = [
        'lat'         => 'float',
        'lng'         => 'float',
        'price'       => 'float',
        'geocoded'    => 'boolean',
        'newly_built' => 'boolean',
        'listed_at'   => 'datetime',
    ];
}
