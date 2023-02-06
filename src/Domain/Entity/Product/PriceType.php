<?php

namespace App\Domain\Entity\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Company\Company;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[Entity]
class PriceType
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    #[Groups(['Product'])]
    private ?int $id = null;

    #[Column]
    #[Groups(['Product'])]
    private string $name;

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Product'])]
    private ?Company $company = null;
}