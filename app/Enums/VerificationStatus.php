<?php
namespace App\Enums;

enum VerificationStatus: string {
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
}