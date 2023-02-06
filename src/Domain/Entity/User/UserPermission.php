<?php

namespace App\Domain\Entity\User;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Repository\User\UserPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPermissionRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_OWNER')"],
    ],
    itemOperations: [
        'get' => ['security' => "(is_granted('ROLE_OWNER') and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()) 
                    or (object.getUser() == user)"], 
        'put' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))  
                    and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()"], 
        'patch' => ['security' => "(is_granted('ROLE_OWNER') or (is_granted('ROLE_USER') and is_granted('EMPLOYERS', object)))  
                    and user.getEmployeeCompany() == object.getUser().getEmployeeCompany()"]
    ]
)]

class UserPermission implements UserPermissionConstants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'permission', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ApiProperty(identifier: true)]
    private User $user;

    #[ORM\Column(type: 'json')]
    private array $permissions = [];

    /**
     * @param User $user
     * @param array $permissions
     */
    public function __construct(User $user, array $permissions = UserPermission::DEFAULT_PERMISSIONS)
    {
        $this->user = $user;
        $this->permissions = $permissions;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }
}
