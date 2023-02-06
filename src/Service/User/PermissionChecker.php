<?php

namespace App\Service\User;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\User\User;

class PermissionChecker
{
    public function check(User $user, string $permission): bool
    {
        $permissions = $user->getPermission()?->getPermissions() ;
        return $permissions && !empty($permissions[$permission]);

    }

    public function checkEmployeePermission(Employee $employee, string $permission): bool
    {
        return $this->check($employee->getUser(), $permission);
    }
}