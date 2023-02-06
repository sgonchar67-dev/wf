<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\Contractor\Contractor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class ContractorContact
{
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Contractor::class, inversedBy: 'contacts')]
    private Contractor $contractor;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?string $department = null;

    public static function create(Contractor $contractor, ?string $name, ?string $phone = null, ?string $email = null): self
    {
        $self = new self();
        $self->contractor = $contractor;
        $self->name = $name;
        $self->phone = $phone;
        $self->email = $email;

        return $self;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setName(?string $name): ContractorContact
    {
        $this->name = $name;
        return $this;
    }

    public function setPhone(?string $phone): ContractorContact
    {
        $this->phone = $phone;
        return $this;
    }

    public function setEmail(?string $email): ContractorContact
    {
        $this->email = $email;
        return $this;
    }

    public function setDepartment(?string $department): ContractorContact
    {
        $this->department = $department;
        return $this;
    }
}
