<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource]
#[ApiFilter(NumericFilter::class, properties: ['company.id'])]
class ContractorStatus
{
    private const DEFAULT_COLOR = '#90A4AE';

    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['Contractor', 'Company:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Contractor'])]
    private Company $company;

    #[ORM\Column(nullable: false)]
    #[Groups(['Contractor', 'Company:read'])]
    private string $name;

    #[ORM\Column(length: 16, nullable: false)]
    #[Groups(['Contractor', 'Company:read'])]
    private string $color = self::DEFAULT_COLOR;

    #[Pure] public static function create(Company $company, string $name, ?string $color = null): self
    {
        $self = new self();
        $self->company = $company;
        $self->name = $name;
        $self->color = $color ?: self::DEFAULT_COLOR;
        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor($color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }
}
