<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingArea extends Model
{
    protected $fillable = ['name', 'code', 'capacity', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}