<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function terminalFrom()
    {
        return $this->belongsTo(Terminal::class, 'from_terminal_id', 'id');
    }

    public function terminalTo()
    {
        return $this->belongsTo(Terminal::class, 'to_terminal_id', 'id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}
