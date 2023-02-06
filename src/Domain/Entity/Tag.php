<?php

namespace App\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\MappedSuperclass]
abstract class Tag
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(length: 32)]
    protected string $value = '';

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Product'])]
    protected ?Company $company = null;

    public function __construct(string $value, ?Company $company = null)
    {
        $this->value = $value;
        $this->company = $company;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setValue(string $value): Tag
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): static
    {
        $this->company = $company;
        return $this;
    }
}
