<?php

namespace App\Domain\Entity\Company;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Requisites;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => ['security' => "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"]
    ],
    itemOperations: [
        'get',
        'put' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
        'patch' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
        'delete' => ['security' => "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getCompany().getUser() == user)"],
    ],
    denormalizationContext: ['groups' => ['Organization', 'Organization:write']],
    normalizationContext: ['groups' => ['Organization', 'Organization:read']]
)]
class Organization
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    #[Groups(['Organization:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'organizations')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Organization'])]
    private Company $company;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['Organization'])]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 16, nullable: false)]
    #[Groups(['Organization'])]
    private ?string $type = null;

    #[ORM\Column(type: 'string', length: 5, nullable: false)]
    #[Groups(['Organization'])]
    private ?string $typeOpfId = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['Organization'])]
    private ?string $fio = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['Organization'])]
    private ?string $actualAddress = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['Organization'])]
    private ?string $legalAddress = null;

    #[ORM\Column(type: 'string', length: 12, nullable: false)]
    #[Groups(['Organization'])]
    private ?string $inn = null;

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    #[Groups(['Organization'])]
    private ?string $ogrn = null;

    #[ORM\Column(type: 'string', length: 8, nullable: false)]
    #[Groups(['Organization'])]
    private ?string $okved = null;

    /**
     * @var Collection<int, Requisites>
    */
    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: Requisites::class, orphanRemoval: true)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Organization:read'])]
    private Collection $requisites;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Groups(['Organization'])]
    private ?bool $main = null;

    #[Pure] public function __construct()
    {
        $this->requisites = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;
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
    public function getRequisites()
    {
        return $this->requisites;
    }

    /**
     * @param Collection|Requisites[] $requisites
     */
    public function setRequisites($requisites): self
    {
        $this->requisites->clear();
        foreach ($requisites as $requisite) {
            $this->addRequisite($requisite);
        }
        return $this;
    }

    public function addRequisite(Requisites $requisite): self
    {
        if (!$this->requisites->contains($requisite)) {
            $this->requisites->add($requisite);
        }
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
