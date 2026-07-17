<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['booking_number', 'customer_id', 'provider_profile_id', 'provider_service_id', 'service_area_id', 'scheduled_at', 'customer_name', 'customer_email', 'customer_phone', 'customer_address', 'customer_city', 'customer_state', 'customer_pincode', 'issue_description', 'estimated_price', 'final_price', 'status', 'cancel_reason', 'reject_reason', 'completed_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => BookingStatus::class,
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }

    public function serviceArea()
    {
        return $this->belongsTo(ProviderServiceArea::class);
    }
}
