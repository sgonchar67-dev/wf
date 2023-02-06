<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[Entity]
class Attribute
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Company::class)]
    #[Groups(['Product'])]
    private Company $company;

    #[Column]
    #[Groups(['Product'])]
    private string $name;

    public function __construct(Company $company, string $name)
    {
        $this->company = $company;
        $this->name = $name;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @param \App\Domain\Entity\Company\Company $company
     * @return Attribute
     */
    public function setCompany(Company $company): Attribute
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Attribute
     */
    public function setName(string $name): Attribute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}