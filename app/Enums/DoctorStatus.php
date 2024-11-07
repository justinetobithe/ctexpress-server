<?php
namespace App\Enums;

enum DoctorStatus: string {
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
}