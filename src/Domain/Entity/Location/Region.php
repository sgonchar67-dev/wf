<?php

namespace App\Domain\Entity\Location;

use App\Domain\Entity\Location\City;
use App\Domain\Entity\Location\Country;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use OpenApi\Annotations as OA;

#[Entity]
#[Table(name: 'wrf_base_region')]
class Region
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private ?int $id = null;
    #[ManyToOne(targetEntity: 'Country', inversedBy: 'regions')]
    #[JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private Country $country;
    #[Column(type: 'string')]
    private string $name;
    #[Column(name: 'name_translit', type: 'string')]
    private string $transliteratedName;
    #[Column(name: 'active', type: 'boolean')]
    private bool $isActive;
    #[Column(name: 'is_index', type: 'string')]
    private int $index;
    /**
     * @var Collection<int, City>
     */
    #[OneToMany(mappedBy: 'region', targetEntity: City::class, cascade: ['persist', 'remove'])]
    private Collection $cities;
    #[Pure] public function __construct()
    {
        $this->cities = new ArrayCollection();
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getCountry(): Country
    {
        return $this->country;
    }
    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }
    public function getName(): string
    {
        return $this->name;
    }
}
