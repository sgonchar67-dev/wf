<?php

namespace App\Security\Voter\ContractorInviteToken;

use App\Domain\Entity\Company\Employee;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Domain\Entity\User\UserPermissionConstants;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;
use App\Service\User\PermissionChecker;
use Symfony\Component\Security\Core\Security;

class ContractorInviteTokenSecurity
{
    public function __construct(
        private SecurityInterface $security,
    ) {
    }

    public function isGrantedToCreate(ContractorInviteToken $inviteToken): bool
    {
        $employee = $this->security->getUser()->getEmployee();
        return $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_SALES)
            && $inviteToken->getContractor()->getCompany() === $employee->getCompany();
    }

    public function isGrantedToImplement(ContractorInviteToken $inviteToken): bool
    {
        return ($this->security->isGranted(UserRolesConstants::ROLE_ADMIN_CONTRACTORS)
            || $this->security->isGranted(UserRolesConstants::ROLE_ADMIN_PURCHASES))
            && !$inviteToken->isImplemented();
    }
}