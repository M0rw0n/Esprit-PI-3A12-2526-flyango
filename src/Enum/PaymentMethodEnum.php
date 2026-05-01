<?php

namespace App\Enum;

enum PaymentMethodEnum: string
{
    case CARD = 'card';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case STRIPE = 'stripe';
    case SQUARE = 'SQUARE';
    case DEMO = 'DEMO';
}
