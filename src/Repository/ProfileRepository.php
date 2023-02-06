<?php

namespace App\Repository;

use App\Domain\Entity\User\Profile;
use Doctrine\ORM\EntityManagerInterface;

class ProfileRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Profile::class);
    }
}