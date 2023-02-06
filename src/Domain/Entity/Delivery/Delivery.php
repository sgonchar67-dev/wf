<?php

namespace App\Domain\Entity\Delivery;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use ApiPlatform\Core\Annotation\ApiResource;
use DomainException;

use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Domain\Entity\Showcase\Showcase;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    denormalizationContext: ['groups' => ['Delivery', 'Delivery:write']],
    normalizationContext: ['groups' => [
        'Delivery',
        'Delivery:read',
        'Delivery:ReferenceBookValue',
    ]],
)]
#[ApiFilter(BooleanFilter::class, properties: ['checked'])]
#[Entity]
class Delivery
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Groups(['Delivery:read', 'OrderEventLog:read'])]
    private ?int $id = null;
    #[Column]
    #[Groups(['Delivery', 'OrderEventLog:read'])]
    private string $name;
    #[Column(type: 'string')]
    #[Groups(['Delivery'])]
    private ?string $description = '';
    #[Column(type: 'boolean')]
    #[Groups(['Delivery'])]
    private bool $checked = false;

    #[Column(name: 'price', type: 'decimal', scale: 2)]
    #[Groups(['Delivery'])]
    private ?float $price = null;

    #[ManyToOne(targetEntity: Showcase::class, inversedBy: 'deliveries')]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Delivery'])]
    #[ApiProperty(example: '/api/showcases/75')]
    private ?Showcase $showcase = null;

//    #[Column]
//    private array $location = [];
//    #[Column]
//    private array $terminal = [];
//    #[Column(type: 'boolean')]
//    private bool $pickup = true;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function checked(): bool
    {
        return $this->checked;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;
        return $this;
    }

    public function setName(string $name): self
    {
        $exists = $this->getShowcase()
            ->getDeliveries()
            ->exists(fn($k, $v) => $v === $name);
        if ($exists && $name !== $this->name) {
            throw new DomainException("Delivery named {$name} is already exists");
        }

        $this->name = $name;
        return $this;
    }

    public function isSystem(): bool
    {
        return !$this->showcase;
    }

    public function getShowcase(): Showcase
    {
        return $this->showcase;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setShowcase(Showcase $showcase): self
    {
        $this->showcase = $showcase;
        return $this;
    }

    public function edit(ReferenceBookValue $rbvDeliveryMethod, bool $checked, ?string $description): self
    {
        $this->rbvDeliveryMethod = $rbvDeliveryMethod;
        $this->checked = $checked;
        $this->description = $description;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return Delivery
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}