<?php

namespace App\Domain\Entity\Order\OrderProduct\Embeddable;

use App\Domain\Entity\Product\ProductPackage;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[Embeddable]
class PackageEmbedded
{
    #[Column(nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private string $name;

    #[Column(nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private int $quantity;

    #[Column(nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?string $measure = null;

    #[Column(name: 'weight', type: 'float', scale: 4, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $weight = 0;

    #[Column(nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?string $weightMeasure = null;

    #[Column(name: 'volume', type: 'float', scale: 4, nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?float $volume = 0;

    #[Column(nullable: true)]
    #[Groups(['OrderProduct:read'])]
    private ?string $volumeMeasure = null;

    public function __construct(?ProductPackage $productPackage = null)
    {
        if (!$productPackage) {
            return;
        }
        $this->name = $productPackage->getPackType()?->getName();
        $this->quantity = $productPackage->getQuantity();
        $this->measure = $productPackage->getMeasure();
        $this->weight = $productPackage->getWeight();
        $this->weightMeasure = $productPackage->getRbvWeightMeasure()?->getValue();
        $this->volume = $productPackage->getVolume();
        $this->volumeMeasure = $productPackage->getRbvVolumeMeasure()?->getValue();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string|null
     */
    public function getMeasure(): ?string
    {
        return $this->measure;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return mixed|string|null
     */
    public function getWeightMeasure(): mixed
    {
        return $this->weightMeasure;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return mixed|string|null
     */
    public function getVolumeMeasure(): mixed
    {
        return $this->volumeMeasure;
    }
}