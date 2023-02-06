<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Company\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
#[ApiResource]
#[ApiFilter(NumericFilter::class, properties: [
    'company.id'
])]
class ContractorGroup
{
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(nullable: false)]
    private float $discount = 0;

    #[ORM\Column(type: 'boolean', nullable: false)] // Hidden
    private bool $automatically = false;

    #[ORM\Column(nullable: false)] // Hidden
    private float $autoFrom = 0;

    #[ORM\Column(nullable: false)] // Hidden
    private float $autoTo = 0;

    #[ORM\Column(type: 'smallint', nullable: false)] // Hidden
    private int $typeAuto = 0;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Contractor::class)]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $contractors;

    #[Pure] public function __construct(Company $company, string $name)
    {
        $this->contractors = new ArrayCollection();
        $this->company = $company;
        $this->name = $name;
    }

    public function getId(): ?int
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

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    public function isAutomatically(): bool
    {
        return $this->automatically;
    }

    public function setAutomatically(bool $automatically): self
    {
        $this->automatically = $automatically;
        return $this;
    }

    public function getAutoFrom(): float
    {
        return $this->autoFrom;
    }

    public function setAutoFrom(float $autoFrom): self
    {
        $this->autoFrom = $autoFrom;
        return $this;
    }

    public function getAutoTo(): float
    {
        return $this->autoTo;
    }

    public function setAutoTo(float $autoTo): self
    {
        $this->autoTo = $autoTo;
        return $this;
    }

    /**
     * @return Collection|Contractor[]
     */
    public function getContractors(): Collection
    {
        return $this->contractors;
    }

    public function addContractor(Contractor $contractor): self
    {
        if (!$this->contractors->contains($contractor)) {
            $this->contractors[] = $contractor;
            $contractor->setGroup($this);
        }
        return $this;
    }

    public function removeContractor(Contractor $contractor): self
    {
        $this->contractors->removeElement($contractor);
        return $this;
    }

    public function getTypeAuto(): int
    {
        return $this->typeAuto;
    }

    public function setTypeAuto(int $typeAuto): self
    {
        $this->typeAuto = $typeAuto;
        return $this;
    }
}
