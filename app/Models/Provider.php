<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'devise_token',
        'device_type',
        'provider',
        'provider_id',
    ];
}
