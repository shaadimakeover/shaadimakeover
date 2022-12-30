<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistPricing extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'pricing_service_id', 'price', 'description'];

    public function pricingService()
    {
        return $this->belongsTo(PricingService::class, 'pricing_service_id');
    }
}
