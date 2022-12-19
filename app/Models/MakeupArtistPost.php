<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistPost extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'post_title', 'post_desc', 'post_attachment', 'status'];

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }
}
