<?php
namespace App\DTO\Employee;

use App\Domain\Entity\User\UserPermission;
use App\Domain\Entity\User\UserPermissionConstants;
use Symfony\Component\Serializer\Annotation\Groups;

class EmployeeEditDto
{
    /** Profile side */
    #[Groups(['Employee:write'])]
    private string $profileName;

    #[Groups(['Employee:write'])]
    private ?string $profileSurname = null;

    #[Groups(['Employee:write'])]
    private ?string $profilePatronymic = null;

    /** Employee side */
    #[Groups(['Employee:write'])]
    private ?string $employeeDescription = null;

    /** UserPermission side */
    #[Groups(['Employee:write'])]
    private array $userPermissions = UserPermissionConstants::DEFAULT_PERMISSIONS;

    public function getProfileName(): string
    {
        return $this->profileName;
    }

    public function setProfileName(string $profileName): self
    {
        $this->profileName = $profileName;

        return $this;
    }

    public function getProfileSurname(): ?string
    {
        return $this->profileSurname;
    }

    public function setProfileSurname(?string $profileSurname): self
    {
        $this->profileSurname = $profileSurname;

        return $this;
    }

    public function getProfilePatronymic(): ?string
    {
        return $this->profilePatronymic;
    }

    public function setProfilePatronymic(?string $profilePatronymic): self
    {
        $this->profilePatronymic = $profilePatronymic;

        return $this;
    }

    public function getEmployeeDescription(): ?string
    {
        return $this->employeeDescription;
    }

    public function setEmployeeDescription(?string $employeeDescription): self
    {
        $this->employeeDescription = $employeeDescription;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getUserPermissions(): array
    {
        return $this->userPermissions;
    }

    /**
     * @param string[]|null $userPermissions
     * @return self
     */
    public function setUserPermissions(array $userPermissions): self
    {
        $this->userPermissions = $userPermissions;

        return $this;
    }
}
