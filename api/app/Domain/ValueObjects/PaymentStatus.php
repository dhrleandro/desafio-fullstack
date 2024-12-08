<?php

namespace App\Domain\ValueObjects;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}
