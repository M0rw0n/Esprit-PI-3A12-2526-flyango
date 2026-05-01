<?php

namespace App\Enum;

enum BookingStatusEnum: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case PAID = 'PAID';
    case COMPLETED = 'COMPLETED';
}
