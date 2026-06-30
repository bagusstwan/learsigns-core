<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'gesture_type',
        'landmarks',
    ];

    /**
     * Konversi otomatis JSON ke Array
     */
    protected $casts = [
        'landmarks' => 'array',
    ];
}