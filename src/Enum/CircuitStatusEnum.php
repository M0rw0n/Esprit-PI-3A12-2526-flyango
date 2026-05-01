<?php

namespace App\Enum;

enum CircuitStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ARCHIVED = 'archived';
}
