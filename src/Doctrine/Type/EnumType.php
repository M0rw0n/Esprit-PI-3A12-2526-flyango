<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = $fieldDeclaration['values'] ?? [];
        if (empty($values)) {
            return 'VARCHAR(255)';
        }
        $formatted = implode(', ', array_map(fn($v) => "'$v'", $values));
        return "ENUM($formatted)";
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
        return 'enum';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public static function getEnumValues(): array
    {
        return [
            'user_role_enum' => ['ROLE_USER', 'ROLE_ADMIN'],
            'booking_status_enum' => ['PENDING', 'CONFIRMED', 'CANCELLED'],
            'travel_class_enum' => ['ECONOMY', 'PREMIUM_ECONOMY', 'BUSINESS', 'FIRST'],
            'car_category_enum' => ['ECONOMY', 'COMPACT', 'MIDSIZE', 'FULLSIZE', 'LUXURY', 'SUV', 'VAN'],
            'car_transmission_enum' => ['MANUAL', 'AUTOMATIC'],
            'car_fuel_enum' => ['GASOLINE', 'DIESEL', 'ELECTRIC', 'HYBRID'],
            'offer_type_enum' => ['FLIGHT', 'HOTEL', 'CAR', 'PACKAGE'],
            'voyage_type_enum' => ['SOLO', 'COUPLE', 'FAMILY', 'GROUP'],
            'vehicle_type_enum' => ['SEDAN', 'SUV', 'VAN', 'LUXURY'],
            'transport_type_enum' => ['FLIGHT', 'TRAIN', 'BUS', 'CAR', 'TAXI'],
        ];
    }
}

class DatabaseEnumType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(255)';
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
        return 'database_enum';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
