<?php

namespace App\Domain\Entity\Product;

use App\Domain\Entity\Product\PriceType;
use App\Domain\Entity\Product\Product;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource]
#[Entity]
class ProductPrice
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Product'])]
    private ?int $id = null;

    #[Column]
    #[Groups(['Product'])]
    private string $name;

    #[ManyToOne(targetEntity: Product::class, inversedBy: 'prices')]
    #[JoinColumn(onDelete: 'CASCADE')]
    private Product $product;

    #[ManyToOne(targetEntity: PriceType::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Product'])]
    private PriceType $priceType;

    #[Column(type: 'decimal', scale: 2)]
    #[Groups(['Product'])]
    private ?float $price;

    public function __construct(Product $product, PriceType $priceType, string $name, float $price)
    {
        $this->product = $product;
        $this->priceType = $priceType;
        $this->price = $price;
        $this->name = $name;
    }

    public function __clone() {
        $this->id = null;
    }

    public function edit(Product $product, PriceType $priceType, string $name, float $price): self
    {
        $this->priceType = $priceType;
        $this->price = $price;
        $this->product = $product;
        $this->name = $name;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getPriceType(): PriceType
    {
        return $this->priceType;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPriceType(PriceType $priceType): ProductPrice
    {
        $this->priceType = $priceType;
        return $this;
    }

    public function setPrice(?float $price): ProductPrice
    {
        $this->price = $price;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ProductPrice
     */
    public function setName(string $name): ProductPrice
    {
        $this->name = $name;
        return $this;
    }


}