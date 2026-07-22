<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'about', 'experience', 'profile_image', 'is_available', 'average_rating'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function serviceAreas()
    {
        return $this->hasMany(ProviderServiceArea::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
