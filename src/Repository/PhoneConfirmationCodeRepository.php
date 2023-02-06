<?php

namespace App\Repository;

use App\Domain\Entity\User\PhoneConfirmationCode;
use App\Domain\Entity\User\User;
use App\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class PhoneConfirmationCodeRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(PhoneConfirmationCode::class);
    }

    public function getByUser(User $user): PhoneConfirmationCode
    {

        if (!$code = $this->repo->findOneBy([
            'user' => $user,
            'phone' => $user->getPhone()
        ])) {
            throw new NotFoundException();
        }

        return $code;
    }
}