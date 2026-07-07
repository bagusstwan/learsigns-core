<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'quest_id', 'completed_at'];
}