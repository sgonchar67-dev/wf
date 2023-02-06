<?php

namespace App\Security\Voter\Company;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;

class CompanySecurity
{
    public function __construct(private SecurityInterface $security)
    {
    }

    public function isGrantedToActivate(Company $company): bool
    {
        $user = $this->security->getUser();
        return $this->security->isGranted(UserRolesConstants::ROLE_OWNER)
            && $company === $user->getCompany();
    }

    public function isGrantedToUpdate(Company $company): bool
    {
        $user = $this->security->getUser();
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_COMPANY)
            && $company === $user->getEmployeeCompany();
    }

    public function isGrantedToDelete(Company $company): bool
    {
        $user = $this->security->getUser();
        return $this->security->isGranted(UserRolesConstants::ROLE_OWNER)
            && $company === $user->getCompany();
    }
}