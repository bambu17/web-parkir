<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'ticket_number', 'user_id', 'parking_area_id', 'vehicle_id', 'plate_number',
        'vehicle_type', 'entry_at', 'exit_at', 'total_amount', 'status',
    ];

    protected function casts(): array
    {
        return ['entry_at' => 'datetime', 'exit_at' => 'datetime', 'total_amount' => 'decimal:2'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(ParkingArea::class, 'parking_area_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}