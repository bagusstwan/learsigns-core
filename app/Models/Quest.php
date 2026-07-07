<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    protected $fillable = ['title', 'description', 'target_gesture', 'reward_stars', 'is_active'];
}