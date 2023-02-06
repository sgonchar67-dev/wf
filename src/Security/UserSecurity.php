<?php

namespace App\Security;

use App\Domain\Entity\User\User;
use App\Repository\User\UserRepository;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;

class UserSecurity implements SecurityInterface
{
    #[Pure] public function __construct(
        private Security $security,
        private UserRepository $userRepository
    ) {
    }

    public function getUser(): ?User
    {
        if (!$userIdentity = $this->security->getUser()) {
            return null;
        }

        return $this->userRepository->loadUserByIdentifier($userIdentity->getUserIdentifier());
    }

    public function isGranted($attribute, $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }
}