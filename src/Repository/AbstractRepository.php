<?php

namespace App\Repository;

use App\Helper\ObjectHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use ReflectionClass;
use ReflectionException;
use App\Exception\NotFoundException;

abstract class AbstractRepository
{
    use SaveEntityRepositoryTrait;

    protected EntityRepository $repo;

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {}

    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * @throws ReflectionException
     * @throws NotFoundException
     */
    public function get($id)
    {
        if (!$entity = $this->repo->find($id)) {
            $reflect = new ReflectionClass($this->repo->getClassName());
            $name = $reflect->getShortName();
            throw new NotFoundException("Entity $name $id not found");
        }

        return $entity;
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findByIds(array $ids): array
    {
        return $this->repo->findBy(['id' => $ids]);
    }

    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    public function remove($entity)
    {
        $this->entityManager->remove($entity);
    }

    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function getPaginator($page, $limit = 100): Paginator
    {
        $dql = $this->repo->createQueryBuilder('p')
            ->getQuery();

        return $this->paginate($dql, $page, $limit);
    }

    protected function paginate($dql, $page = 1, $limit = 100): Paginator
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}
