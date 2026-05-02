<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CarTransmissionType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('MANUAL', 'AUTOMATIC')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value;
    }

    public function getName(): string
    {
        return 'car_transmission_enum';
    }
}
