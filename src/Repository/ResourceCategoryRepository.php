<?php

namespace App\Repository;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Company\ResourceCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @method ResourceCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceCategory[]    findAll()
 * @method ResourceCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceCategoryRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(ResourceCategory::class));
    }

    /**
     * @param Company $company
     * @return array|string
     */
    public function getTree(Company $company): array|string
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('
                node.id,
                node.title,
                node.description,
                node.slug,
                node.lft,
                node.rgt,
                node.level,
                COUNT(p.id) as productCount')
            ->from(ResourceCategory::class, 'node')
            ->leftJoin('node.products', 'p')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.company = :company')
            ->setParameter('company', $company)
            ->groupBy('node.id')
            ->getQuery()
        ;
        return $this->buildTree($query->getArrayResult());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findRoot(Company $company): ?ResourceCategory
    {
        return parent::getRootNodesQueryBuilder()
            ->andWhere('node.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
