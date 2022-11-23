<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistProfile extends Model
{
    use HasFactory;
    protected $fillable = ['formal_name', 'expert_id', 'introduction', 'working_since', 'can_do_makeup_at', 'is_featured', 'rating', 'place_availability'];
}
