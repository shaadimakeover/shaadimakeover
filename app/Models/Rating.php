<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable=['user_id','artist_id','rating'];

    public function user()
    {
       return $this->belongsTo(User::class,'user_id');
    }

    public function artist()
    {
       return $this->belongsTo(User::class,'artist_id');
    }
}
