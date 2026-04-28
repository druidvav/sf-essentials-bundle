<?php

namespace Druidvav\EssentialsBundle\Measurement;

use Druidvav\EssentialsBundle\Exception\DimensionsException;

class Dimensions
{
    /** Вес в виде "12.34 cm" или просто "12.34" */
    public const SYSTEM_CM = 'cm';
    /** Вес в виде "12 mm" или просто "12" */
    public const SYSTEM_MM = 'mm';
    /** Вес в виде "12.34 inch" или просто "12.34" */
    public const SYSTEM_INCH = 'inch';

    protected $k = 1;
    protected $system = self::SYSTEM_CM;
    protected $x;
    protected $y;
    protected $z;

    public static function create($x, $y, $z, $system): ?self
    {
        if (empty($x) || empty($y) || empty($z)) {
            return null;
        }

        return new self($x, $y, $z, $system);
    }

    public static function createMm($x, $y, $z): ?self
    {
        if (empty($x) || empty($y) || empty($z)) {
            return null;
        }

        return new self($x, $y, $z, self::SYSTEM_MM);
    }

    /**
     * @param float  $x
     * @param float  $y
     * @param float  $z
     * @param string $system
     *
     * @throws DimensionsException
     */
    public function __construct($x, $y, $z, $system = null)
    {
        $this->setSystem($system);
        $x = (float) $x;
        $y = (float) $y;
        $z = (float) $z;
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

    public function setSystem($system): self
    {
        switch ($system) {
            case self::SYSTEM_CM: $this->k = 10;
                break;
            case self::SYSTEM_MM: $this->k = 1;
                break;
            case self::SYSTEM_INCH: $this->k = 25.4;
                break;
            default: throw new DimensionsException('Unknown measurement system');
        }
        $this->system = $system;

        return $this;
    }

    public function getSystem()
    {
        return $this->system;
    }

    public function getMmX(): int
    {
        return (int) $this->x;
    }

    public function getCmX(): float
    {
        return round($this->x / 10, 1);
    }

    public function getLocalX(): float
    {
        return round($this->x / $this->k, 1);
    }

    public function getMmY(): int
    {
        return (int) $this->y;
    }

    public function getCmY(): float
    {
        return round($this->y / 10, 1);
    }

    public function getLocalY(): float
    {
        return round($this->y / $this->k, 1);
    }

    public function getMmZ(): int
    {
        return (int) $this->z;
    }

    public function getCmZ(): float
    {
        return round($this->z / 10, 1);
    }

    public function getLocalZ(): float
    {
        return round($this->z / $this->k, 1);
    }

    public function getMaxLengthMm(): int
    {
        return max($this->getMmX(), $this->getMmY(), $this->getMmZ());
    }

    public function getSumLengthMm(): int
    {
        return $this->getMmX() + $this->getMmY() + $this->getMmZ();
    }

    public function getMmPretty(): string
    {
        return $this->getMmX().'x'.$this->getMmY().'x'.$this->getMmZ().' mm';
    }

    public function getCmPretty(): string
    {
        return $this->getCmX().'x'.$this->getCmY().'x'.$this->getCmZ().' cm';
    }

    public function getPretty(): string
    {
        return $this->getLocalX().'x'.$this->getLocalY().'x'.$this->getLocalZ().' '.$this->getSystem();
    }

    public function getVolumeWeightG($k = 5000): int
    {
        return (int) (($this->getMmX() * $this->getMmY() * $this->getMmZ()) / $k);
    }
}
