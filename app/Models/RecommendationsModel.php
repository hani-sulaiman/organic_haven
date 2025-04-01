<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationsModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'count_data_trained',
        'trained_at',
    ];

    protected $dates = [
        'trained_at',
    ];
}
