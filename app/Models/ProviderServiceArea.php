<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderServiceArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_profile_id',
        'pincode',
        'city',
        'state',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}