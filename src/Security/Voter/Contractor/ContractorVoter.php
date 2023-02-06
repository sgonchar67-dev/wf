<?php

namespace App\Security\Voter\Contractor;

use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserRolesConstants;
use App\Security\SecurityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ContractorVoter extends Voter
{
    private const CONTRACTOR_UPDATE = 'CONTRACTOR_UPDATE';
    private const CONTRACTOR_DELETE = 'CONTRACTOR_DELETE';
    private const CONTRACTOR_ATTACH_COMPANY = 'CONTRACTOR_ATTACH_COMPANY';

    private const ATTRIBUTES = [
        self::CONTRACTOR_UPDATE,
        self::CONTRACTOR_DELETE,
        self::CONTRACTOR_ATTACH_COMPANY,
    ];

    public function __construct(
        private ContractorSecurity $contractorSecurity,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::ATTRIBUTES)
            && $subject instanceof Contractor;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Contractor $contractor */
        $contractor = $subject;

        return match ($attribute) {
            self::CONTRACTOR_UPDATE => $this->contractorSecurity->isGrantedToUpdate($contractor),
            self::CONTRACTOR_DELETE => $this->contractorSecurity->isGrantedToDelete($contractor),
        };
    }
}
