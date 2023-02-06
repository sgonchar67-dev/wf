<?php

namespace App\Repository;

use App\Domain\Entity\Showcase\Showcase;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @method ShowcaseCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowcaseCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowcaseCategory[]    findAll()
 * @method ShowcaseCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowcaseCategoryRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(ShowcaseCategory::class));
    }

    /**
     * @param Showcase|int $showcase
     * @return array|string
     */
    public function getTree($showcase): array|string
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
            ->from(ShowcaseCategory::class, 'node')
            ->leftJoin('node.products', 'p')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.showcase = :showcase')
            ->setParameter('showcase', $showcase)
            ->groupBy('node.id')
            ->getQuery()
        ;
        return $this->buildTree($query->getArrayResult());
    }

}
