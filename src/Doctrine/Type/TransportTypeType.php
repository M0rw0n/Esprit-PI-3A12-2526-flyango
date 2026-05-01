<?php

namespace App\Doctrine\Type;

use App\Enum\TransportTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TransportTypeType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('FLIGHT', 'TRAIN', 'BUS', 'CAR', 'TAXI', 'Avion', 'Bus', 'Train', 'Taxi', 'Vélocation')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TransportTypeEnum
    {
        if ($value instanceof TransportTypeEnum || $value === null) {
            return $value;
        }

        return TransportTypeEnum::tryFrom((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof TransportTypeEnum ? $value->value : $value;
    }

    public function getName(): string
    {
        return 'transport_type_enum';
    }
}
