<?php

namespace App\Domain\Entity\Company;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Employee\GetOrderManagersAction;
use App\Controller\Employee\DeleteEmployeeAction;
use App\Domain\Entity\Company\Company;
use App\DTO\Employee\EmployeeCreateDto;
use App\DTO\Employee\EmployeeEditDto;
use App\Domain\Entity\User\Profile;
use App\Domain\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_ADMIN')"],
        'post' => ['security' => "is_granted('ROLE_USER')",
            'input' => EmployeeCreateDto::class,
        ],
        'get_orders_managers' => [
            'method' => 'GET',
            'pagination_enabled' => false,
            'path' => '/employees/orders_managers',
            'controller' => GetOrderManagersAction::class,
            'deserialize' => false,
        ],
    ],
    itemOperations: [
        'get' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object))) 
                    and (object.getCompany() == user.getEmployee().getCompany())",
            'normalization_context' => [ 'groups'=> ['Employee', 'Employee:read', 'Employee:item:read']]
        ],
        'put' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object))) 
                    and (object.getCompany() == user.getEmployee().getCompany())",
            'input' => EmployeeEditDto::class,     
            'normalization_context' => [ 'groups'=> ['Employee', 'Employee:read', 'Employee:item:read']]
        ],
        'patch' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object))) 
                    and (object.getCompany() == user.getEmployee().getCompany())",
            'normalization_context' => [ 'groups'=> ['Employee', 'Employee:read', 'Employee:item:read']]
        ],
        'delete' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object))) 
                and (object.getCompany() == user.getEmployee().getCompany())",
            'controller' => DeleteEmployeeAction::class,
        ]
    ],
    attributes: ["pagination_client_items_per_page" => true],
    denormalizationContext: ['groups' => ['Employee', 'Employee:write']],
    normalizationContext: ['groups' => ['Employee', 'Employee:read']]
)]
#[ORM\Entity]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Employee:read', 'UserPermissionTemplate:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'employee', targetEntity: User::class, cascade: [
        'persist',
        'remove', //delete Employee -> delete Employee also in transaction
    ])]
    #[ORM\JoinColumn(
        unique: true,
        nullable: false,
        onDelete: 'CASCADE' // delete User -> cascade delete Employee
    )]
    #[Groups(['Employee:read', 'UserPermissionTemplate:read'])]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: "employees")]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups(['Employee:read'])]
    private Company $company;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['Employee'])]
    private ?string $description;

    #[ORM\Column(name: 'blocked', type: 'boolean', options: ['default' => false])]
    #[Groups(['Employee'])]
    private bool $blocked = false;

    #[ORM\Column(name: 'deleted', type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;

    public function delete(): static
    {
        if (!$this->deleted) {
            $this->blocked = true;
            $this->deleted = true;
            $this->user->delete();
        }

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    #[Pure] public static function create(User $user, Company $company): Employee
    {
        $employee = new self();
        $employee->user = $user;
        $employee->company = $company;
        return $employee;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Employee
    {
        $this->user = $user;
        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): Employee
    {
        $this->company = $company;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->getUser()->getProfile()->getName();
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

    #[Pure] #[Groups(['Employee:read', 'UserPermissionTemplate:read'])]
    public function getProfileDetail(): Profile
    {
        return $this->getUser()->getProfile();
    }
    
    #[Pure] #[Groups(['Employee:item:read'])]
    public function getUserPermission(): array
    {
        return $this->getUser()->getPermission()?->getPermissions() ?? [];
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $isBlocked): self
    {
        $this->blocked = $isBlocked;

        return $this;
    }
}
