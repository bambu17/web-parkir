<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingRate extends Model
{
    protected $fillable = ['vehicle_type', 'first_hour_rate', 'next_hour_rate', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'first_hour_rate' => 'decimal:2', 'next_hour_rate' => 'decimal:2'];
    }
}