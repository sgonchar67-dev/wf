<?php

namespace App\Controller;

use App\Domain\Entity\User\User;
use Symfony\Component\HttpKernel\Attribute\AsController;


#[AsController]
class GetCurrentUserAction extends AbstractController
{
    public function __invoke(): ?User
    {
        return $this->getUser();
    }
}