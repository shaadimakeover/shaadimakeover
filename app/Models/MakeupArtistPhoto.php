<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistPhoto extends Model
{
    use HasFactory;
    protected $fillable = ['expert_id', 'category', 'photo', 'is_top_photo'];
}
