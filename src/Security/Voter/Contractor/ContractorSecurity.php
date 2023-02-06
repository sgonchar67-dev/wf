<?php

namespace App\Security\Voter\Contractor;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;
use App\Service\User\PermissionChecker;

class ContractorSecurity
{
    public function __construct(
        private SecurityInterface $security,
        private PermissionChecker $permissionChecker,
    ) {
    }

    private function checkPermission(Employee $employee): bool
    {
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_EMPLOYERS)
            || $this->permissionChecker->checkEmployeePermission($employee, UserPermissionConstants::CONTRACTORS);
    }

    public function isGrantedToUpdate(Contractor $contractor): bool
    {
        $employee = $this->security->getUser()?->getEmployee();
        return
            $this->checkPermission($employee) &&
            $contractor->getCompany() === $employee?->getCompany();
    }

    public function isGrantedToDelete(Contractor $contractor): bool
    {
        $employee = $this->security->getUser()?->getEmployee();
        return
            $this->checkPermission($employee) &&
            $contractor->getCompany() === $employee->getCompany();
    }
}