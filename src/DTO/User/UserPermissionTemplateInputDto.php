<?php
namespace App\DTO\User;

use App\Domain\Entity\User\User;
use Symfony\Component\Serializer\Annotation\Groups;

final class UserPermissionTemplateInputDto
{
    /** @var string|null */
    #[Groups(['UserPermissionTemplate:write'])]
    private ?string $description = null;

    /** @var User[]|null */
    #[Groups(['UserPermissionTemplate:write'])]
    private ?array $users = null;

    /** @var string[]|null */
    #[Groups(['UserPermissionTemplate:write'])]
    private ?array $templatePermission = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return User[]|null
     */
    public function getUsers(): ?array
    {
        return $this->users;
    }

    /**
     * @param User[]|null $users
     * @return self
     */
    public function setUsers(?array $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getTemplatePermission(): ?array
    {
        return $this->templatePermission;
    }

    public function setTemplatePermission(?array $templatePermission): self
    {
        $this->templatePermission = $templatePermission;

        return $this;
    }
}
