<?php

namespace App\Doctrine\Type;

use App\Enum\UserRoleEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UserRoleType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('ROLE_USER', 'ROLE_ADMIN')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserRoleEnum
    {
        if ($value instanceof UserRoleEnum || $value === null) {
            return $value;
        }

        return UserRoleEnum::tryFrom((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof UserRoleEnum ? $value->value : $value;
    }

    public function getName(): string
    {
        return 'user_role_enum';
    }
}
