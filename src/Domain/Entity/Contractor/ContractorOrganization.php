<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Contractor\ContractorRequisites;
use App\Domain\Entity\Company\Requisites;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ['security' => "is_granted('ROLE_USER')"]
    ],
    itemOperations: [
        'get',
        'put' => ['security' => "is_granted('ROLE_USER')"],
        'patch' => ['security' => "is_granted('ROLE_USER')"],
        'delete' => ['security' => "is_granted('ROLE_USER')"],
    ],
    denormalizationContext: ['groups' => ['ContractorOrganization', 'ContractorOrganization:write']],
    normalizationContext: ['groups' => ['ContractorOrganization', 'ContractorOrganization:read']]
)]
class ContractorOrganization
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    #[Groups(['ContractorOrganization:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Contractor::class, cascade: ['persist'], inversedBy: 'organizations')]
    #[Groups(['ContractorOrganization'])]
    private Contractor $contractor;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 16, nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $type;

    #[ORM\Column(type: 'string', length: 5, nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $typeOpfId;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $fio;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $actualAddress;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $legalAddress;

    #[ORM\Column(type: 'string', length: 12, nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $inn;

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $ogrn;

    #[ORM\Column(type: 'string', length: 8, nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private string $okved;

    /**
     * @var Collection<int, Requisites>
    */
    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: ContractorRequisites::class, orphanRemoval: true)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['ContractorOrganization:read'])]
    private Collection $requisites;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Groups(['ContractorOrganization'])]
    private bool $main = false;

    #[Pure] public function __construct()
    {
        $this->requisites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContractor(): Contractor
    {
        return $this->contractor;
    }

    public function setContractor(Contractor $contractor): self
    {
        $this->contractor = $contractor;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeOpfId(): string
    {
        return $this->typeOpfId;
    }

    public function setTypeOpfId(string $typeOpfId): self
    {
        $this->typeOpfId = $typeOpfId;
        return $this;
    }

    public function getFio(): string
    {
        return $this->fio;
    }

    public function setFio(string $fio): self
    {
        $this->fio = $fio;
        return $this;
    }

    public function getActualAddress(): string
    {
        return $this->actualAddress;
    }

    public function setActualAddress(string $actualAddress): self
    {
        $this->actualAddress = $actualAddress;
        return $this;
    }

    public function getLegalAddress(): string
    {
        return $this->legalAddress;
    }

    public function setLegalAddress(string $legalAddress): self
    {
        $this->legalAddress = $legalAddress;
        return $this;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function setInn(string $inn): self
    {
        $this->inn = $inn;
        return $this;
    }

    public function getOgrn(): string
    {
        return $this->ogrn;
    }

    public function setOgrn(string $ogrn): self
    {
        $this->ogrn = $ogrn;
        return $this;
    }

    public function getOkved(): string
    {
        return $this->okved;
    }

    public function setOkved(string $okved): self
    {
        $this->okved = $okved;
        return $this;
    }

    /**
     * @return Collection<int, Requisites>
     */
    public function getRequisites(): Collection
    {
        return $this->requisites;
    }

    public function addRequisite(ContractorRequisites $requisite): self
    {
        if (!$this->requisites->contains($requisite)) {
            $this->requisites->add($requisite);
        }
        return $this;
    }

    public function removeRequisite(ContractorRequisites $requisite): self
    {
        $this->requisites->removeElement($requisite);
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
