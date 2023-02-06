<?php

namespace App\Domain\Entity\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Controller\Company\ActivateCompanyAction;
use App\Controller\Company\GetCustomersAction;
use App\Controller\Company\GetSuppliersAction;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Document;
use App\Domain\Entity\Image;
use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserPermissionTemplate;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_USER')",
//            'security' => "is_granted('ROLE_ADMIN')",
//            "security_message" => "Only admins can read companies.",
        ],
        'get_customers' => [
            'security' => "is_granted('ROLE_USER')",
            'method' => 'get',
            'path' => '/companies/customers',
            'controller' => GetCustomersAction::class,
            'pagination_enabled' => false,
        ],
        'get_suppliers' =>  [
            'security' => "is_granted('ROLE_USER')",
            'method' => 'get',
            'path' => '/companies/suppliers',
            'controller' => GetSuppliersAction::class,
            'pagination_enabled' => false,
        ],
    ],
    itemOperations: [
        'get',
        'put' => ['security' => "is_granted('COMPANY_UPDATE', object)"],
        'patch' => ['security' => "is_granted('COMPANY_UPDATE', object)"],
        'put_activate' => [
            'security' => "is_granted('COMPANY_ACTIVATE', object)",
            'method' => 'put',
            'path' => '/companies/{id}/activate',
            'controller' => ActivateCompanyAction::class,
        ],
    ],
    attributes: ["pagination_client_items_per_page" => true],
    denormalizationContext: ['groups' => ['Company', 'Company:write']],
    normalizationContext: ['groups' => ['Company', 'Company:read']]
)]
#[ApiFilter(BooleanFilter::class,
    properties: ['isPartner'],
//    arguments: ['customer', 'supplier', 'all']
)]
class Company
{
    /**
     * @todo dto
     */
    #[ApiProperty(iri: 'https://schema.org/attachedToContractor')]
    #[Groups(['Company:read'])]
    public ?Contractor $attachedToContractor = null;

    #[ORM\Id]
    #[ORM\Column(length: 11, options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    #[Groups(['Company:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'company', targetEntity: User::class, orphanRemoval: true)]
    #[ORM\JoinColumn(unique: true, nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Company'])]
    private User $user;

    #[ORM\Column]
    #[Groups(['Company'])]
    private string $name;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['Company'])]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Company'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Company'])]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Company'])]
    private ?string $website = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['Company'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Company:read'])]
    private ?DateTime $activatedAt = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['Company'])]
    #[ApiProperty(readable: true)]
    private bool $activated = false;
    
    #[ORM\Column]
    #[Groups(['Company:read'])]
    private DateTime $createdAt;

    #[ORM\OneToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['Company'])]
    private ?Employee $contactPerson = null;

    /**
     * @var Collection<int, Organization>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Organization::class, cascade: ['persist'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Company:read'])]
    private Collection $organizations;

    /**
     * @var Collection<int, UserPermissionTemplate>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: UserPermissionTemplate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Company:read'])]
    private Collection $userPermissionTemplates;
    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Employee::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Company:read'])]
    private Collection $employees;

    #[ORM\OneToOne(mappedBy: 'company', targetEntity: Showcase::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['Company:read'])]
    private ?Showcase $showcase = null;

    /**
     * @var Collection<int, ResourceCategory>
     */
    #[OneToMany(mappedBy: 'company', targetEntity: ResourceCategory::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource(maxDepth: 1)]
    #[Groups(['Company:read'])]
    private Collection $categories;

    /**  @var Collection<int, Contractor> */
    #[ApiSubresource(maxDepth: 1)]
    #[ApiProperty(readableLink:false)]
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Contractor::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Company'])]
    private Collection $contractors;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ApiProperty(iri: 'https://schema.org/logo')]
    #[Groups(['Company'])]
    private ?Image $logo = null;

     /**
     * @var Collection<int, Document>
     */
    #[ORM\ManyToMany(targetEntity: Document::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['Company'])]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $documents;

    #[ORM\Column(name: 'deleted', type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;

    public function __construct(User $user, string $name = null)
    {
        $this->user = $user;
        $this->name = $name;
        $this->createdAt = new DateTime();

        $this->organizations = new ArrayCollection();
        $this->userPermissionTemplates = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->contractors = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Company
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Company
    {
        $this->user = $user;
        return $this;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Company
    {
        $this->name = $name;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): Company
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): Company
    {
        $this->address = $address;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Company
    {
        $this->email = $email;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Company
    {
        $this->website = $website;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Company
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Company
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getShowcase(): ?Showcase
    {
        return $this->showcase;
    }

    public function getActivatedAt(): DateTime|null
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(DateTime $activatedAt): Company
    {
        $this->activatedAt = $activatedAt;
        return $this;
    }

    public function getUserPermissionTemplates(): Collection
    {
        return $this->userPermissionTemplates;
    }

    public function hasAdminPermissionTemplate(): bool
    {
        $count = $this->userPermissionTemplates->filter(
            fn(UserPermissionTemplate $t) => $t->getDescription() === UserPermissionConstants::DEFAULT_PERMISSIONS_NAME
        )->count();

        return (bool) $count;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
        }
        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        $this->employees->removeElement($employee);
        return $this;
    }

    public function setActivated(bool $activated): Company
    {
        $this->activated = $activated;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->activated;
    }

    /**
     * @return Employee|null
     */
    public function getContactPerson(): ?Employee
    {
        return $this->contactPerson;
    }

    /**
     * @param Employee|null $contactPerson
     * @return Company
     */
    public function setContactPerson(?Employee $contactPerson): Company
    {
        if ($contactPerson && !$this->employees->contains($contactPerson)) {
            throw new \DomainException(
                "The employee {$contactPerson->getId()} does not belong to the company {$this->id}"
            );
        }
        $this->contactPerson = $contactPerson;
        return $this;
    }

    public function getLogo(): ?Image
    {
        return $this->logo;
    }

    public function setLogo(?Image $logo): self
    {
        $this->logo = $logo;

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
            $this->documents[] = $document;
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        $this->documents->removeElement($document);

        return $this;
    }

    /**
     * @return Collection<int, Contractor>
     */ 
    public function getContractors(): Collection
    {
        return $this->contractors;
    }

    public function addUserPermissionTemplate(UserPermissionTemplate $template): self
    {
        if (!$this->userPermissionTemplates->contains($template)) {
            $this->userPermissionTemplates->add($template);
            $template->setCompany($this);
        }

        return $this;
    }

    public function removeUserPermissionTemplate(UserPermissionTemplate $template): self
    {
        $this->userPermissionTemplates->removeElement($template);

        return $this;
    }

    public function addContractor(Contractor $contractor): self
    {
        if (!$this->contractors->contains($contractor)) {
            $this->contractors[] = $contractor;
        }

        return $this;
    }

    public function removeContractor(Contractor $contractor): self
    {
        $this->contractors->removeElement($contractor);

        return $this;
    }

    public function delete(): static
    {
        if (!$this->deleted) {
            $this->deleted = true;
        }
        return $this;
    }

    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function activate(): self
    {
        $this->activated = true;
        $this->activatedAt = new DateTime();

        if (!$this->hasAdminPermissionTemplate()) {
            $permissionTemplate = new UserPermissionTemplate(
                $this,
                UserPermissionConstants::DEFAULT_PERMISSIONS_NAME,
                UserPermissionConstants::DEFAULT_PERMISSIONS
            );
            $this->addUserPermissionTemplate($permissionTemplate);
        }

        if (!$this->getRootCategory()) {
            $root = ResourceCategory::createRoot($this);
            $this->addCategory($root);
        }

        return $this;
    }


    public function addCategory(ResourceCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    public function removeCategory(ResourceCategory $resourceCategory): self
    {
        $this->categories->removeElement($resourceCategory);
        return $this;
    }


    public function getRootCategory(): ?ResourceCategory
    {
        $root = null;
        /** @var ResourceCategory|null $first */
        if ($first = $this->categories->first()) {
            $root = $first->getRoot();
        }
        return $root;
    }
}
