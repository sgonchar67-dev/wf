<?php

namespace App\Repository;

use App\Domain\Entity\User\EmailConfirmationCode;
use App\Domain\Entity\User\User;
use App\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class EmailConfirmationCodeRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(EmailConfirmationCode::class);
    }

    public function getByUser(User $user): EmailConfirmationCode
    {

        if (!$code = $this->repo->findOneBy([
            'user' => $user,
            'email' => $user->getEmail()
        ])) {
            throw new NotFoundException();
        }

        return $code;
    }
}