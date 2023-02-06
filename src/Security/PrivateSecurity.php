<?php

namespace App\Security;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\User\User;
use App\Exception\PrivateSecurityException;
use JetBrains\PhpStorm\Pure;

class PrivateSecurity
{
    private const COMPANIES = [46, 574];
    private const USERS = [84, 710];
    private const SHOWCASES = [73, 63680];

    #[Pure(true)] public function isProtected($entity): bool
    {
        return match ($entity::class) {
            User::class => in_array($entity->getId(), self::USERS),
            Company::class => in_array($entity->getId(), self::COMPANIES),
            Showcase::class => in_array($entity->getId(), self::SHOWCASES),
            default => false
        };
    }

    /**
     * @throws PrivateSecurityException
     */
    public function check($entity): void
    {
        if ($this->isProtected($entity)) {
            throw new PrivateSecurityException($entity::class. " {$entity->getId()} is protected");
        }
    }
}