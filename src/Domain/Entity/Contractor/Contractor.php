<?php

namespace App\Domain\Entity\Contractor;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use App\Controller\Contractor\DeleteContractorAction;
use App\Controller\Contractor\GenerateContractorAction;
use App\Controller\Contractor\UpdateContractorAction;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Document;
use App\Domain\Entity\Product\ProductTag;
use App\Domain\Entity\User\Profile;
use App\Validator\UniqueProperty\UniqueProperty;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_ADMIN_CONTRACTORS')",],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN_CONTRACTORS')",
        ],
        'post_generate' => [
            'security' => "is_granted('ROLE_ADMIN_CONTRACTORS')",
            'security_post_denormalize' => "is_granted('ROLE_ADMIN_CONTRACTORS')",
            'method' => 'POST',
            'path' => '/contractors/generate',
            'controller' => GenerateContractorAction::class,
            'denormalization_context' => ['groups' => ['Contractor:generate']],
            'deserialize' => false,
        ],
    ],
    itemOperations: [
        'get',
        'put' => [
            'security' => "is_granted('CONTRACTOR_UPDATE', object)",
            'controller' => UpdateContractorAction::class,
        ],
        'patch' => [
            'security' => "is_granted('CONTRACTOR_UPDATE', object)",
            'controller' => UpdateContractorAction::class,
        ],
        'delete' => [
            'security' => "is_granted('CONTRACTOR_DELETE', object)",
            'controller' => DeleteContractorAction::class,
        ],
//        'delete_all' => [
//            'security' => "is_granted('ROLE_OWNER')",
//            'method' => 'delete',
//            'path' => '/contractors/all',
//            'controller' => DeleteAllContractorAction::class,
//            'deserialize' => false,
//        ],
    ],
    attributes: ["pagination_client_items_per_page" => true],
    denormalizationContext: ['groups' => ['Contractor', 'Contractor:write']],
    normalizationContext: ['groups' => ['Contractor', 'Contractor:read']]
)]
#[ORM\Entity]
#[ApiFilter(NumericFilter::class, properties: [
    'company.id',
    'contractorCompany.id'
])]
class Contractor
{
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['Contractor:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['Contractor'])]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 2000, nullable: true)]
    #[Groups(['Contractor'])]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['Contractor'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Contractor'])]
    private ?string $website = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'email', nullable: true)]
    #[Groups(['Contractor'])]
    private ?string $email = null;
    
    #[ORM\Column(type: 'datetime')]
    #[Groups(['Contractor'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['Contractor'])]
    private bool $blocked = false;

    #[ORM\Column(length: 2000, nullable: true)]
    #[Groups(['Contractor'])]
    private ?string $note = null;
    
    // TODO: Нормализовать название property (владелец контрагента)
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: "contractors")]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Contractor'])]
    #[UniqueProperty(entity: Company::class)]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Contractor'])]
    #[UniqueProperty(entity: Company::class)]
    private ?Company $contractorCompany = null;

    #[ApiProperty(readableLink:false)]
    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Contractor'])]
    private ?Employee $manager = null;

    #[ORM\ManyToOne(targetEntity: ContractorStatus::class, cascade: ["persist"])]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[Groups(['Contractor', 'Company:read'])]
    private ?ContractorStatus $status = null;

    #[ORM\ManyToOne(targetEntity: ContractorGroup::class, inversedBy: 'contractors')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['Contractor'])]
    private ?ContractorGroup $group = null;

    /** @var Collection<int, Document> */
    #[ORM\ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Contractor'])]
    private Collection $documents;

    /**  @var Collection<int, ContractorTag> */
    #[ORM\ManyToMany(targetEntity: ContractorTag::class)]
    #[ORM\JoinTable(name: 'contractors_tags')]
    #[ORM\JoinColumn(name: "contractor_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "tag_id", referencedColumnName: "id")]
    private Collection $tags;

    /**  @var Collection<int, ContractorContact> */
    #[ApiSubresource(maxDepth: 1)]
    #[ORM\OneToMany(mappedBy: 'contractor', targetEntity: ContractorContact::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Contractor'])]
    private Collection $contacts;

    /**  @var Collection<int, ContractorProperty> */
    #[ApiSubresource(maxDepth: 1)]
    #[ORM\ManyToMany(targetEntity: ContractorProperty::class)]
    #[Groups(['Contractor'])]
    private Collection $properties;

    /**
     * @var Collection<int, ContractorOrganization>
     */
    #[ORM\OneToMany(mappedBy: 'contractor', targetEntity: ContractorOrganization::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Contractor'])]
    private Collection $organizations;

    public function __construct(Company $company, string $name)
    {
        $this->documents = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->organizations = new ArrayCollection();

        $this->company = $company;
        $this->name = $name;

        $this->createdAt = new DateTime();
    }

    public static function create(Company $company, Company $contractorCompany): static
    {
        $self = new self($company, $contractorCompany->getName());
        $self->setContractorCompany($contractorCompany);
        $self->name = $contractorCompany->getName();
        $self->description = $contractorCompany->getDescription();
        $self->address = $contractorCompany->getAddress();
        $self->phone = $contractorCompany->getPhone();
        $self->email = $contractorCompany->getEmail();

        $self->status = ContractorStatus::create($company, 'New');
        $profile = $contractorCompany->getUser()->getProfile();

        $contact = ContractorContact::create($self, $profile->getName(), $profile->getPhone(), $profile->getName());
        $self->addContact($contact);

        return $self;
    }

    #[Groups(['Company:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['Company:read'])]
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    #[Groups(['Contractor'])]
    public function getStatus(): ?ContractorStatus
    {
        return $this->status;
    }

    public function setStatus(?ContractorStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getGroup(): ?ContractorGroup
    {
        return $this->group;
    }

    public function setGroup(?ContractorGroup $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
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


    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        $this->documents->removeElement($document);

        return $this;
    }

    /**
     * @return Collection<int, ProductTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(ProductTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
        return $this;
    }

    public function removeTag(ProductTag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getContractorCompany(): ?Company
    {
        return $this->contractorCompany;
    }

    #[Groups(['Contractor'])]
    #[Pure] public function getContractorCompanyName(): ?string
    {
        return $this->contractorCompany?->getName();
    }

    public function setContractorCompany(?Company $contractorCompany): self
    {
        if ($this->contractorCompany === $this->company) {
            throw new \DomainException();
        }
        $this->contractorCompany = $contractorCompany;
        return $this;
    }

    /**
     * @return Collection<int, ContractorContact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(ContractorContact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setContractor($this);
        }
        return $this;
    }

    public function removeContact(ContractorContact $contact): self
    {
        $this->contacts->removeElement($contact);
        return $this;
    }

    /**
     * @return Collection<int, ContractorProperty>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(ContractorProperty $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties[] = $property;
        }
        return $this;
    }

    public function removeProperty(ContractorProperty $property): self
    {
        $this->properties->removeElement($property);
        return $this;
    }
 
    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked($blocked): static
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return Collection<int, ContractorOrganization>
     */
    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(ContractorOrganization $organization): self
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations[] = $organization;
        }
        return $this;
    }

    public function removeOrganization(ContractorOrganization $organization): self
    {
        $this->organizations->removeElement($organization);
        return $this;
    }

    public function getManager(): ?Employee
    {
        return $this->manager;
    }

    public function setManager(?Employee $manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    #[Groups(['Contractor:read'])]
    public function getManagerDetail(): ?Profile
    {
        return $this->getManager()?->getUser()->getProfile();
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }
}
