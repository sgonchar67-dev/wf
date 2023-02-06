<?php
namespace App\DTO\User;

use App\Domain\Entity\Company\Employee;
use Symfony\Component\Serializer\Annotation\Groups;

final class UserPermissionTemplateOutputDto
{
    /** @var int */
    #[Groups(['UserPermissionTemplate:read'])]
    private int $id;

    #[Groups(['UserPermissionTemplate:read'])]
    private string $company;

    #[Groups(['UserPermissionTemplate:read'])]
    private string $description;

    #[Groups(['UserPermissionTemplate:read'])]
    private array $templatePermission = [];

    /** @var Employee[]|null */
    #[Groups(['UserPermissionTemplate:read'])]
    private ?array $employees;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        
        $this->company = $company;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTemplatePermission(): array
    {
        return $this->templatePermission;
    }

    /**
     * @var string[]
     * @return  self
     */ 
    public function setTemplatePermission(array $templatePermission): self
    {
        $this->templatePermission = $templatePermission;

        return $this;
    }

    /**
     * @param Employee[]|null $employees
     *
     * @return  self
     */ 
    public function setEmployees(?array $employees): self
    {
        $this->employees = $employees;

        return $this;
    }

    /**
     * @return Employee[]|null
     */
    public function getEmployees(): ?array
    {
        return $this->employees;
    }
}