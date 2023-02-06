<?php

namespace App\Security;

use App\Domain\Entity\User\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

interface SecurityInterface extends AuthorizationCheckerInterface
{
    public function getUser(): ?User;
}