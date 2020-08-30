<?php
namespace Druidvav\EssentialsBundle\Measurement;

/**
 * Dimensions trait for Doctrine Entities
 * @package Druidvav\EssentialsBundle\Measurement
 * @property int $dimensionX mm
 * @property int $dimensionY mm
 * @property int $dimensionZ mm
 * @noinspection PhpUnused
 */
trait DimensionsTrait
{
    public abstract function getLengthSystem(): string;

    public function setDimensions(Dimensions $decorator = null): bool
    {
        $changed = false;
        if ($decorator === null) {
            if (null != $this->dimensionX || null != $this->dimensionY || null != $this->dimensionZ) {
                $changed = true;
            }
            $this->dimensionX = null;
            $this->dimensionY = null;
            $this->dimensionZ = null;
        } else {
            if ($decorator->getMmX() != $this->dimensionX
                || $decorator->getMmY() != $this->dimensionY
                || $decorator->getMmZ() != $this->dimensionZ) {
                $changed = true;
            }
            $this->dimensionX = $decorator->getMmX();
            $this->dimensionY = $decorator->getMmY();
            $this->dimensionZ = $decorator->getMmZ();
        }
        return $changed;
    }

    public function getDimensions(): ?Dimensions
    {
        return Dimensions::createMm($this->dimensionX, $this->dimensionY, $this->dimensionZ);
    }

    public function getLocalDimensions(): ?Dimensions
    {
        return $this->getDimensions() ? $this->getDimensions()->setSystem($this->getLengthSystem()) : null;
    }

    public function getDimensionX(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getMmX() : null;
    }

    public function getDimensionY(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getMmY() : null;
    }

    public function getDimensionZ(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getMmZ() : null;
    }

    public function getDimensionCmX(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getCmX() : null;
    }

    public function getDimensionCmY(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getCmY() : null;
    }

    public function getDimensionCmZ(): ?int
    {
        return $this->getDimensions() ? $this->getDimensions()->getCmZ() : null;
    }

    public function getLocalDimensionsPretty(): ?string
    {
        return $this->getLocalDimensions() ? $this->getLocalDimensions()->getPretty() : null;
    }

    public function getDimensionsMmPretty(): ?string
    {
        return $this->getDimensions() ? $this->getDimensions()->getMmPretty() : null;
    }

    public function getDimensionsCmPretty(): ?string
    {
        return $this->getDimensions() ? $this->getDimensions()->getCmPretty() : null;
    }
}