<?php

namespace App\Repository\User;

use App\Domain\Entity\User\User;
use App\Domain\Entity\User\UserPermissionTemplate;
use App\Exception\NotFoundException;
use App\Helper\PhoneHelper;
use App\Repository\SaveEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method save(object $entity)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    use SaveEntityRepositoryTrait;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @throws NotFoundException
     */
    public function get($id): User
    {
        /** @var User|null $user */
        if (!$user = $this->find($id)) {
            throw new NotFoundException("User {$id} not found");
        }
        return $user;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function loadUserByIdentifier(string $identifier): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Domain\Entity\User\User u
                WHERE 
                u.id = :identifier OR
                u.phone = :identifier OR
                u.email = :identifier'
        )
            ->setParameter('identifier', $identifier)
            ->getOneOrNullResult();
    }


    public function loadUserByUsername(string $username): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Domain\Entity\User\User u
                WHERE 
                u.phone = :username OR
                u.email = :username'
        )
            ->setParameter('username', $username)
            ->getOneOrNullResult();
    }

    public function loadUserByEmail($email): ?User
    {
        if (!$user = $this->findOneByEmail($email)) {
            return null;
        }
        if (!$user->isEmailConfirmed()) {
//            throw new AuthenticationException("Email is not confirmed");
            return null;
        }

        return $user;
    }

    public function loadUserByPhone($phone): ?User
    {
        $phone = PhoneHelper::format($phone);
        if (!$user = $this->findOneByPhone($phone)) {
            return null;
        }
        if (!$user->isPhoneConfirmed()) {
//            throw new AuthenticationException("Phone is not confirmed");
            return null;
        }

        return $user;
    }

    public function findOneByUsername(?string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function findByPhone(?string $phone): ?User
    {
        return $this->findOneBy(['phone' => $phone]);
//        return $this->findOneBy(['phone.number' => $phone]);
    }

    public function findByEmail(?string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @param UserPermissionTemplate $userPermissionTemplate
     * @return User[]
     */
    public function getUsersWithAttachedTemplate(UserPermissionTemplate $userPermissionTemplate): array
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->innerJoin('u.employee', 'e')
            ->where('e.company = :company')
            ->andWhere('e.description = :description')
            ->setParameters(['company' => $userPermissionTemplate->getCompany(), 'description' => $userPermissionTemplate->getDescription()])
            ->getQuery();

        return $query->getResult();
    }
}
