<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class OfferTypeType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('FLIGHT', 'HOTEL', 'CAR', 'PACKAGE')";
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
        return 'offer_type_enum';
    }
}
