<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistPhoto extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'photo_album_id', 'photo'];
}
