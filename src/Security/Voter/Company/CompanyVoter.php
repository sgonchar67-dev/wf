<?php

namespace App\Security\Voter\Company;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\User\User;
use App\Security\SecurityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CompanyVoter extends Voter
{
    private const COMPANY_UPDATE = 'COMPANY_UPDATE';
    private const COMPANY_ACTIVATE = 'COMPANY_ACTIVATE';
    private const COMPANY_DELETE = 'COMPANY_DELETE';

    private const ATTRIBUTES = [
        self::COMPANY_UPDATE,
        self::COMPANY_DELETE,
        self::COMPANY_ACTIVATE,
    ];

    public function __construct(
        private SecurityInterface $security,
        private CompanySecurity $companySecurity,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::ATTRIBUTES)
            && $subject instanceof Company;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Company $company */
        $company = $subject;

        return match ($attribute) {
            self::COMPANY_UPDATE => $this->companySecurity->isGrantedToUpdate($company),
            self::COMPANY_DELETE => $this->companySecurity->isGrantedToDelete($company),
            self::COMPANY_ACTIVATE => $this->companySecurity->isGrantedToActivate($company),
        };
    }
}
