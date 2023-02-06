<?php

namespace App\Domain\Entity\Location;

use App\Domain\Entity\Location\Region;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
#[Table(name: 'wrf_base_city')]
class City
{
    #[Id]
    #[Column(name: 'id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    #[GeneratedValue]
    private ?int $id = null;
    #[ManyToOne(targetEntity: Region::class, inversedBy: 'cities')]
    #[JoinColumn(name: 'region_id', referencedColumnName: 'id')]
    private Region $region;
    #[Column(type: 'string')]
    private string $name;
    #[Column(name: 'name_translit', type: 'string')]
    private string $transliteratedName;
    #[Column(name: 'active', type: 'boolean')]
    private bool $isActive;
    #[Column(name: 'is_index', type: 'string')]
    private int $index;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getRegion(): Region
    {
        return $this->region;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getTransliteratedName(): string
    {
        return $this->transliteratedName;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getIndex(): int
    {
        return $this->index;
    }


}
