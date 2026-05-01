<?php

namespace App\Doctrine\Type;

use App\Enum\BookingStatusEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class BookingStatusType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('PENDING', 'CONFIRMED', 'CANCELLED', 'PAID', 'COMPLETED')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BookingStatusEnum
    {
        if ($value instanceof BookingStatusEnum || $value === null) {
            return $value;
        }

        return BookingStatusEnum::tryFrom((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof BookingStatusEnum ? $value->value : $value;
    }

    public function getName(): string
    {
        return 'booking_status_enum';
    }
}
