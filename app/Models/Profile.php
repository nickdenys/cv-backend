<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['title','bio','links'];
    protected $casts = ['links' => 'array'];
}
