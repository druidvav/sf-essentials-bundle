<?php
namespace Druidvav\EssentialsBundle\Measurement;

use Druidvav\EssentialsBundle\Exception\DimensionsException;

class Dimensions
{
    /** Вес в виде "12.34 cm" или просто "12.34" */
    const SYSTEM_CM = 'cm';
    /** Вес в виде "12 mm" или просто "12" */
    const SYSTEM_MM = 'mm';
    /** Вес в виде "12.34 inch" или просто "12.34" */
    const SYSTEM_INCH = 'inch';

    protected $k = 1;
    protected $system = self::SYSTEM_CM;
    protected $x;
    protected $y;
    protected $z;

    public static function create($x, $y, $z, $system)
    {
        if (empty($x) || empty($y) || empty($z)) return null;
        return new self($x, $y, $z, $system);
    }

    public static function createMm($x, $y, $z)
    {
        if (empty($x) || empty($y) || empty($z)) return null;
        return new self($x, $y, $z, self::SYSTEM_MM);
    }

    public function __construct($x, $y, $z, $system = null)
    {
        $this->setSystem($system);
        $x = floatval($x); $y = floatval($y); $z = floatval($z);
        $this->x = $x * $this->k;
        $this->y = $y * $this->k;
        $this->z = $z * $this->k;
        if (($x + $y + $z) > 0 && ($x <= 0 || $y <= 0 || $z <= 0)) {
            throw new DimensionsException('All of the dimensions must be filled or all must be empty');
        }
        if ($x < 0 || $y < 0 || $z < 0) {
            throw new DimensionsException('Invalid length value');
        }
    }

    public function setSystem($system): Dimensions
    {
        switch ($system) {
            case self::SYSTEM_CM: $this->k = 10; break;
            case self::SYSTEM_MM: $this->k = 1; break;
            case self::SYSTEM_INCH: $this->k = 25.4; break;
            default: throw new DimensionsException('Unknown measurement system');
        }
        $this->system = $system;
        return $this;
    }

    public function getSystem()
    {
        return $this->system;
    }

    public function getMmX() { return intval($this->x); }
    public function getCmX() { return round($this->x / 10); }
    public function getLocalX() { return round($this->x / $this->k, 2); }
    public function getMmY() { return intval($this->y); }
    public function getCmY() { return round($this->x / 10); }
    public function getLocalY() { return round($this->y / $this->k, 2); }
    public function getMmZ() { return intval($this->z); }
    public function getCmZ() { return round($this->x / 10); }
    public function getLocalZ() { return round($this->z / $this->k, 2); }

    public function getMaxLengthMm() { return max($this->getMmX(), $this->getMmY(), $this->getMmZ()); }
    public function getSumLengthMm() { return $this->getMmX() + $this->getMmY() + $this->getMmZ(); }

    public function getMmPretty(): string
    {
        return $this->getMmX() . 'x' . $this->getMmY() . 'x' . $this->getMmZ() . ' mm';
    }

    public function getCmPretty(): string
    {
        return $this->getCmX() . 'x' . $this->getCmY() . 'x' . $this->getCmZ() . ' cm';
    }

    public function getPretty(): string
    {
        return $this->getLocalX() . 'x' . $this->getLocalY() . 'x' . $this->getLocalZ() . ' ' . $this->getSystem();
    }
}