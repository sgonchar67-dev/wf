<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Contractor\ContractorAttribute;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity]
class ContractorProperty
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ContractorAttribute::class)]
    private ContractorAttribute $attribute;

    #[ORM\Column]
    private string $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): ContractorAttribute
    {
        return $this->attribute;
    }

    public function setAttribute(ContractorAttribute $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
