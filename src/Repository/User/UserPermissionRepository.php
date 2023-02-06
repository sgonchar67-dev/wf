<?php

namespace App\Repository\User;

use App\Domain\Entity\User\UserPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method \App\Domain\Entity\User\UserPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPermission[]    findAll()
 * @method UserPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPermission::class);
    }

}
