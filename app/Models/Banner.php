<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'thumbnail', 'title', 'menu_order', 'status'];

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id', 'id');
    }
}
