<?php


namespace App\Domain\Entity\Payment;

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

use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Domain\Entity\Showcase\Showcase;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    denormalizationContext: ['groups' => ['PaymentMethod', 'PaymentMethod:write']],
    normalizationContext: ['groups' => [
        'PaymentMethod',
        'PaymentMethod:read',
    ]],
)]
#[ApiFilter(BooleanFilter::class, properties: ['checked'])]
#[Entity]
class PaymentMethod
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    #[Groups(['PaymentMethod'])]
    private ?int $id = null;

    #[Column]
    #[Groups(['PaymentMethod'])]
    private string $name;

    #[Column(nullable: true)]
    #[Groups(['PaymentMethod'])]
    private ?string $description = null;
    #[Column(type: 'boolean', options: ['default' => 0])]
    #[Groups(['PaymentMethod'])]
    private bool $checked = false;
    #[Column(type: 'boolean', options: ['default' => 0] )]
    #[Groups(['PaymentMethod'])]
    private bool $withVat = false;


    #[ManyToOne(targetEntity: Showcase::class, inversedBy: 'paymentMethods')]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['PaymentMethod'])]
    private Showcase $showcase;

    /**
     * Payment constructor.
     */
    public function __construct(Showcase $showcase, string $name)
    {

        $this->showcase = $showcase;
        $this->name = $name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setChecked(bool $checked): PaymentMethod
    {
        $this->checked = $checked;
        return $this;
    }

    public function setShowcase(Showcase $showcase): self
    {
        $this->showcase = $showcase;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isChecked(): bool
    {
        return (bool) $this->checked;
    }

    public function getShowcase(): Showcase
    {
        return $this->showcase;
    }

    public function edit(string $name, bool $checked, string $description): self
    {
        $this->name = $name;
        $this->checked = $checked;
        $this->description = $description;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWithVat(): bool
    {
        return $this->withVat;
    }

    /**
     * @param bool $withVat
     * @return PaymentMethod
     */
    public function setWithVat(bool $withVat): static
    {
        $this->withVat = $withVat;
        return $this;
    }
}