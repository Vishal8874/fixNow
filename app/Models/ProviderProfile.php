<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'about',
        'experience',
        'profile_image',
        'is_available',
        'average_rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}