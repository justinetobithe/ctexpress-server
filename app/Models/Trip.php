<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['total_occupancy'];

    public function terminalFrom()
    {
        return $this->belongsTo(Terminal::class, 'from_terminal_id', 'id');
    }

    public function terminalTo()
    {
        return $this->belongsTo(Terminal::class, 'to_terminal_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trip_id', 'id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function driver()
    {

        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function kiosks()
    {
        return $this->hasMany(kiosk::class, 'trip_id', 'id');
    }

    public function getTotalOccupancyAttribute()
    {
        $bookingsCount = $this->bookings()
            ->where('status', 'approved')
            ->where('paid', 1)
            ->count();

        $kiosksCount = $this->kiosks()
            ->where('paid', 1)
            ->count();

        return $bookingsCount + $kiosksCount;
    }
}
