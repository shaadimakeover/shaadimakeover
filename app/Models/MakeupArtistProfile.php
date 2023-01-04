<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistProfile extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'artist_business_name', 'artist_business_email', 'artist_business_phone', 'artist_location', 'is_featured_artist', 'artist_about', 'artist_working_since', 'artist_can_do_makeup_at'];
}
