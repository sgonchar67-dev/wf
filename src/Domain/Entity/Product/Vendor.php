<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter as APFilter;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[ApiResource]
#[Entity]
#[ApiFilter(APFilter\NumericFilter::class, properties: ['company.id'])]
class Vendor
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;

    #[Column]
    private string $name;

    #[ManyToOne(targetEntity: Company::class)]
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
     * @return \App\Domain\Entity\Company\Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }


}