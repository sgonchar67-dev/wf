<?php

namespace App\Security\Voter\ContractorInviteToken;

use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserRolesConstants;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ContractorInviteTokenVoter  extends Voter
{
    private const CONTRACTOR_INVITE_TOKEN_CREATE = 'CONTRACTOR_INVITE_TOKEN_CREATE';
    private const CONTRACTOR_INVITE_TOKEN_IMPLEMENT = 'CONTRACTOR_INVITE_TOKEN_IMPLEMENT';

    private const ATTRIBUTES = [
        self::CONTRACTOR_INVITE_TOKEN_CREATE,
        self::CONTRACTOR_INVITE_TOKEN_IMPLEMENT,
    ];

    public function __construct(
        private ContractorInviteTokenSecurity $inviteTokenSecurity,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        return in_array($attribute, self::ATTRIBUTES)
            && $subject instanceof ContractorInviteToken;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var ContractorInviteToken $inviteToken */
        $inviteToken = $subject;

        return match ($attribute) {
            self::CONTRACTOR_INVITE_TOKEN_CREATE => $this->inviteTokenSecurity->isGrantedToCreate($inviteToken),
            self::CONTRACTOR_INVITE_TOKEN_IMPLEMENT => $this->inviteTokenSecurity->isGrantedToImplement($inviteToken),
        };
    }
}