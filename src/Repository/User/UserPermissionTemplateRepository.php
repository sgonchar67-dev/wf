<?php

namespace App\Repository\User;

use App\Domain\Entity\User\UserPermissionTemplate;
use App\Repository\SaveEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserPermissionTemplateRepository extends ServiceEntityRepository
{
    use SaveEntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPermissionTemplate::class);
    }
}
