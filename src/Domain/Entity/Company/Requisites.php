<?php

namespace App\Domain\Entity\Company;

use App\Domain\Entity\Company\Organization;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity]
#[ApiResource]
class Requisites
{
    #[Id]
    #[Column(options: ['unsigned' => true])]
    #[GeneratedValue]
    #[Groups(['Requisites:read'])]
    private ?int $id;

    #[ManyToOne(targetEntity: Organization::class, inversedBy: 'requisites')]
    #[JoinColumn(onDelete: 'CASCADE')]
    private Organization $organization;

    #[Column(type: 'string', length: 9, nullable: false)]
    #[Groups(['Requisites'])]
    private ?string $bik = null;

    #[Column(type: 'string', nullable: false)]
    #[Groups(['Requisites'])]
    private ?string $name = null;

    #[Column(type: 'string', nullable: false)]
    #[Groups(['Requisites'])]
    private ?string $address = null;

    #[Column(type: 'string', length: 20, nullable: false)]
    #[Groups(['Requisites'])]
    private ?string $companyCorrespondent = null;

    #[Column(type: 'string', length: 20, nullable: false)]
    #[Groups(['Requisites'])]
    private ?string $account = null;

    #[Column(type: 'boolean', nullable: false)]
    #[Groups(['Requisites'])]
    private ?bool $main = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getBik(): string
    {
        return $this->bik;
    }

    public function setBik(string $bik): self
    {
        $this->bik = $bik;
        return $this;
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCompanyCorrespondent(): string
    {
        return $this->companyCorrespondent;
    }

    public function setCompanyCorrespondent(string $companyCorrespondent): self
    {
        $this->companyCorrespondent = $companyCorrespondent;
        return $this;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function setAccount(string $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getMain(): bool
    {
        return $this->main;
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;
        return $this;
    }
}
