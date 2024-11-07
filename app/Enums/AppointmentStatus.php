<?php
namespace App\Enums;

enum AppointmentStatus: string {
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
}