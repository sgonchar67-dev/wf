<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[Entity]
class PackType
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Product', 'CartProduct:read'])]
    private ?int $id = null;

    #[Column]
    #[Groups(['Product', 'CartProduct:read'])]
    private string $name;

    #[ManyToOne(targetEntity: Company::class)]
    #[Groups(['Product'])]
    private ?Company $company = null;

    /**
     * @param string $name
     * @param \App\Domain\Entity\Company\Company|null $company
     */
    public function __construct(string $name, ?Company $company)
    {
        $this->name = $name;
        $this->company = $company;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }
}