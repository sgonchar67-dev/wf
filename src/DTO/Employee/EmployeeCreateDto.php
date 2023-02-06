<?php
namespace App\DTO\Employee;

use Symfony\Component\Serializer\Annotation\Groups;

final class EmployeeCreateDto extends EmployeeEditDto
{
    /** User side */
    #[Groups(['Employee:write'])]
    private string $userPhone;

    public function getUserPhone(): string
    {
        return $this->userPhone;
    }

    public function setUserPhone(string $userPhone): self
    {
        $this->userPhone = $userPhone;

        return $this;
    }
}
