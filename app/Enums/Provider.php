<?php
namespace App\Enums;

enum Provider: string {
    case CREDENTIALS = 'credentials';
    case GOOGLE = 'google';
}