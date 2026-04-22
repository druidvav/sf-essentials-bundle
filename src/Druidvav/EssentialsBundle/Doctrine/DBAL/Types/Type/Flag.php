<?php

namespace Druidvav\EssentialsBundle\Doctrine\DBAL\Types\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Flag extends Type
{
    public const FLAG = 'flag';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "enum('y','n')";
    }

    /**
     * @param string|null $value
     *
     * @return bool|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return null === $value ? null : 'y' == $value;
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return null === $value ? null : ($value ? 'y' : 'n');
    }

    public function getName()
    {
        return self::FLAG;
    }
}
