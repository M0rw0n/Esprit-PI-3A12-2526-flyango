<?php

namespace App\Doctrine\Type;

use App\Enum\CircuitStatusEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CircuitStatusType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('active', 'inactive', 'draft', 'pending', 'archived')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CircuitStatusEnum
    {
        if ($value instanceof CircuitStatusEnum || $value === null) {
            return $value;
        }

        return CircuitStatusEnum::tryFrom((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof CircuitStatusEnum ? $value->value : $value;
    }

    public function getName(): string
    {
        return 'circuit_status_enum';
    }
}