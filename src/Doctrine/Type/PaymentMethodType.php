<?php

namespace App\Doctrine\Type;

use App\Enum\PaymentMethodEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PaymentMethodType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('card', 'cash', 'bank_transfer', 'stripe', 'SQUARE', 'DEMO')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PaymentMethodEnum
    {
        if ($value instanceof PaymentMethodEnum || $value === null) {
            return $value;
        }

        return PaymentMethodEnum::tryFrom((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof PaymentMethodEnum ? $value->value : $value;
    }

    public function getName(): string
    {
        return 'payment_method_enum';
    }
}