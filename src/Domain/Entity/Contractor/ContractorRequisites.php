<?php

namespace App\Domain\Entity\Contractor;

use App\Domain\Entity\Contractor\ContractorOrganization;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource]
class ContractorRequisites
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: ContractorOrganization::class, inversedBy: 'requisites')]
    private ContractorOrganization $organization;

    #[ORM\Column(type: 'string', length: 9, nullable: false)]
    private string $bik;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $address;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $companyCorrespondent;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $account;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $main = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganization(): ContractorOrganization
    {
        return $this->organization;
    }

    public function setOrganization(ContractorOrganization $organization): self
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
