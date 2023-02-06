<?php

namespace App\Domain\Entity\Location;

use App\Domain\Entity\Location\Region;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use OpenApi\Annotations as OA;

#[Entity]
#[Table(name: 'wrf_base_country')]
class Country
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private ?int $id = null;
    #[Column]
    private string $name;
    #[Column(name: 'name_translit', type: 'string')]
    private string $transliteratedName;
    #[Column(name: 'active', type: 'boolean')]
    private bool $isActive;

    /** @var Collection<int, Region> */
    #[OneToMany(mappedBy: 'country', targetEntity: Region::class, cascade: ['persist', 'remove'])]
    private Collection $regions;

    #[Pure] public function __construct()
    {
        $this->regions = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
}
