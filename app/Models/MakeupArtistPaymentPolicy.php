<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeupArtistPaymentPolicy extends Model
{
    use HasFactory;
    protected $fillable = ['expert_id', 'percentage_of_pay', 'time_to_pay'];
}
