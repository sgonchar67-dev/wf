<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Product\Attribute;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[Entity]
class Property
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Attribute::class)]
    #[Groups(['Product'])]
    private Attribute $attribute;

    #[Column]
    #[Groups(['Product'])]
    private string $value;

    public function __construct(Attribute $attribute, string $value)
    {
        $this->attribute = $attribute;
        $this->value = $value;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Attribute
     */
    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return Property
     */
    public function setAttribute(Attribute $attribute): Property
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Property
     */
    public function setValue(string $value): Property
    {
        $this->value = $value;
        return $this;
    }
}