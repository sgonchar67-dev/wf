<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Auth\CheckUsernameExistsAction;
use App\Controller\GetCurrentUserAction;
use App\DTO\User\CreateUserDto;
use App\DTO\User\UpdateUserDto;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\Employee;
use App\Helper\PhoneHelper;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ["phone"], message: "There is already an account with this phone")]
#[Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'input' => CreateUserDto::class,
        ],
        'get_current' => [
            'method' => 'GET',
            'pagination_enabled' => false,
            'path' => '/users/current',
            'controller' => GetCurrentUserAction::class,
            'deserialize' => false,
        ],
        'check_username_exists' => [
            'pagination_enabled' => false,
            'method' => 'POST',
            'status' => 200,
            'path' => '/users/exists',
            'controller' => CheckUsernameExistsAction::class,
            'defaults' => ['_api_receive' => false],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => ['username' => ['type' => 'string']],
                            ],
                            'example' => ['username' => '79156338686'],
                        ],
                    ],
                ]
            ],
        ],
    ],
    itemOperations: [
//        'get' => ['security' => "((is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object))) and
//                    (object.getCompany() == user.getCompany() or object.getEmployeeCompany() == user.getEmployeeCompany()))
//                    or object == user"],
        'get' ,
        'patch' => [
            'security' => "object == user or 
                            is_granted('ROLE_ADMIN_EMPLOYERS') and object.getEmployeeCompany() == user.getEmployeeCompany()",
            'input' => UpdateUserDto::class,
        ],
        'put' => [
            'security' => "object == user or 
                            is_granted('ROLE_ADMIN_EMPLOYERS') and object.getEmployeeCompany() == user.getEmployeeCompany()",
            'input' => UpdateUserDto::class,
        ],
    ],
    denormalizationContext: ['groups' => ['User', 'User:write', 'User:update', 'UserPermissionTemplate:read']],
    normalizationContext: ['groups' => ['User', 'User:read', 'User:UserNotificationSettings']],
)]
#[ApiFilter(SearchFilter::class, properties: ['username' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, UserRolesConstants
{
    use UserPermissionToRoleMapTrait;

    #[Id]
    #[GeneratedValue]
    #[Column]
    #[Groups(['User:read'])]
    private ?int $id = null;

    #[Column(length: 32, unique: true, nullable: true)]
    #[Groups(['User'])]
    private ?string $phone = null;

    #[Column(unique: true, nullable: true)]
    #[Groups(['User:read'])]
    private ?string $username = null;

    #[Column]
    #[Groups(['User'])]
    private array $roles;

    /**  @var string|null The hashed password */
    #[Column(nullable: true)]
    private ?string $password = null;

    #[Assert\Length(min: 6, max: 22)]
    #[Groups(['User:write'])]
    #[SerializedName(serializedName: 'password')]
    private ?string $plainPassword = null;

    #[Column(unique: true, nullable: true)]
    #[Groups(['User'])]
    private ?string $email = null;

    #[Column(options: ['default' => false])]
    #[Groups(['User:read'])]
    private bool $emailConfirmed = false;

    #[Column(options: ['default' => false])]
    #[Groups(['User:read'])]
    private bool $phoneConfirmed = false;

    #[Column]
    #[Groups(['User:read'])]
    private \DateTime $createdAt;

    #[Column(name: 'deleted', type: 'boolean', options: ['default' => false])]
    private bool $deleted = false;

    #[OneToOne(
        mappedBy: 'user',
        targetEntity: Profile::class,
        cascade: ["persist"],
        orphanRemoval: true
    )]
    #[Groups(['User:read'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Profile $profile = null;

    #[OneToOne(mappedBy: 'user', targetEntity: Company::class, cascade: ['persist'])]
    #[Groups(['User:read'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Company $company = null;

    #[OneToOne(mappedBy: 'user', targetEntity: Employee::class, cascade: ['persist'])]
    #[Groups(['User:read'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?Employee $employee = null;

    #[OneToOne(
        mappedBy: 'user',
        targetEntity: UserNotificationSettings::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true
    )]
    #[Groups(['User:UserNotificationSettings'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?UserNotificationSettings $notificationSettings = null;

    /**
     * @var Collection<int, UserNotificationBot>
     */
    #[OneToMany(mappedBy: 'user', targetEntity: UserNotificationBot::class)]
    #[Groups(['User:UserNotificationBot'])]
    #[ApiSubresource(maxDepth: 1)]
    private Collection $notificationBots;

    #[OneToOne(mappedBy: 'user', targetEntity: UserPermission::class, cascade: ['persist'])]
    #[ApiSubresource(maxDepth: 1)]
    private ?UserPermission $permission = null;

    public function __construct(
        string $phone,
        ?string $email = null,
        ?array $roles = null,
    ) {
        $this->phone = PhoneHelper::format($phone);
        $this->username = PhoneHelper::format($phone);
        $this->roles = $roles ?: [User::ROLE_OWNER, User::ROLE_ADMIN];
        $this->email = $email;
        $this->createdAt = new \DateTime();
        $this->notificationBots = new ArrayCollection();

        $this->notificationSettings = new UserNotificationSettings($this);
        $this->permission = new UserPermission($this);
    }

    public static function create(
        string $phone,
        string $plainPassword,
        string $profileName,
        ?string $email = null,
        ?array $roles = null,
    ): static
    {
        $user = new self($phone, $email, $roles ?: [UserRolesConstants::ROLE_USER]);
        $user->plainPassword = $plainPassword;

        $profile = new Profile($user, $profileName);
        $company = new Company($user, $profile->getName());
        $employee = Employee::create($user, $company);
        $company->addEmployee($employee)
            ->setContactPerson($employee);

        $user->profile = $profile;
        $user->company = $company;
        $user->employee = $employee;

        return $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone !== null ? PhoneHelper::format($phone) : null;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->phone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles ?: [];
        $permissionRoles = $this->getPermissionRoles();
        $roles = array_merge($roles, $permissionRoles);

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        if ($this->email !== $email) {
            $this->emailConfirmed = false;
        }
        $this->email = $email;

        return $this;
    }

    /**
     * @param bool $emailConfirmed
     * @return User
     */
    public function setEmailConfirmed(bool $emailConfirmed): User
    {
        $this->emailConfirmed = $emailConfirmed;
        return $this;
    }

    public function isEmailConfirmed(): ?bool
    {
        return $this->emailConfirmed;
    }

    public function isPhoneConfirmed(): ?bool
    {
        return $this->phoneConfirmed;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Pure] public function getFirstName(): ?string
    {
        return $this->profile?->getName();
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function getNotificationBots(): Collection
    {
        return $this->notificationBots;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getNotificationSettings(): ?UserNotificationSettings
    {
        return $this->notificationSettings;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Profile|null $profile
     * @return User
     */
    public function setProfile(?Profile $profile): User
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @param Employee|null $employee
     * @return User
     */
    public function setEmployee(?Employee $employee): User
    {
        $this->employee = $employee;
        return $this;
    }

    /**
     * @param UserNotificationSettings|null $notificationSettings
     * @return User
     */
    public function setNotificationSettings(?UserNotificationSettings $notificationSettings): User
    {
        $this->notificationSettings = $notificationSettings;
        return $this;
    }

    /**
     * @param Company|null $company
     * @return User
     */
    public function setCompany(?Company $company): User
    {
        $this->company = $company;
        return $this;
    }

    public function changePhone(string $phone): static
    {
        $this->phone = $phone;
        $this->phoneConfirmed = false;
        return $this;
    }

    public function changeEmail(?string $email): static
    {
        $this->email = $email;
        $this->emailConfirmed = false;
//       todo make event UserEmailChangeEvent
        return $this;
    }

    public function setPhoneConfirmed(bool $phoneConfirmed): static
    {
        $this->phoneConfirmed = $phoneConfirmed;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function confirmPhone(): static
    {
        $this->username = $this->phone;
        $this->phoneConfirmed = true;
        return $this;
    }

    #[Pure] public function getEmployeeCompany(): Company
    {
        return $this->employee->getCompany();
    }

    public function getPermission(): ?UserPermission
    {
        return $this->permission;
    }

    public function setPermission(UserPermission $permission): self
    {
        if (empty($this->permission) || $permission->getUser() !== $this) {
             $permission->setUser($this);
        }

        $this->permission = $permission;

        return $this;
    }

    public function hasRoleOwner(): bool
    {
        return in_array(UserRolesConstants::ROLE_OWNER, $this->getRoles());
    }

    public function delete(): static
    {
        if (!$this->deleted) {
            $this->phone = $this->email = $this->username = null;
            $this->deleted = true;
            $this->employee->delete();
            $this->company?->delete();
        }

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
