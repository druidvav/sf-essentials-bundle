<?php
namespace Druidvav\EssentialsBundle\Doctrine\DBAL\Types\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Flag extends Type
{
    const FLAG = 'flag';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "enum('y','n')";
    }

    /**
     * @param string|null $value
     * @param AbstractPlatform $platform
     * @return boolean|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : $value == 'y';
    }

    /**
     * @param boolean $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : ($value ? 'y' : 'n');
    }

    public function getName()
    {
        return self::FLAG;
    }
}