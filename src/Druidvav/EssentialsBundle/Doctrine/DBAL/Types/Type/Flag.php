<?php
namespace Druidvav\EssentialsBundle\Doctrine\DBAL\Types\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Flag extends Type
{
    const FLAG = 'flag';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "enum('y','n')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?bool
    {
        return $value === null ? null : $value == 'y';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value === null ? null : ($value ? 'y' : 'n');
    }

    public function getName(): string
    {
        return self::FLAG;
    }
}