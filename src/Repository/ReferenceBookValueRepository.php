<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\ReferenceBook\ReferenceBook;
use App\Domain\Entity\ReferenceBook\ReferenceBookValue;
use App\Exception\NotFoundException;


/**
 * @method ReferenceBookValue get($id)
 * @method ReferenceBookValue|null find($id)
 * @method remove(ReferenceBookValue $entity)
 * @method delete(ReferenceBookValue $entity)
 * @method persist(ReferenceBookValue $entity)
 * @method save(ReferenceBookValue $entity)
 */
class ReferenceBookValueRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $this->entityManager->getRepository(ReferenceBookValue::class);
    }

    /**
     * @param ReferenceBook $referenceBook
     * @return \App\Domain\Entity\ReferenceBook\ReferenceBookValue[]
     */
    public function findByReferenceBook(ReferenceBook $referenceBook): array
    {
        return $this->repo->findBy(['referenceBook' => $referenceBook->getId()]);
    }

    /**
     * @return \App\Domain\Entity\ReferenceBook\ReferenceBookValue[]
     * @throws NotFoundException
     */
    public function getPackTypes(): array
    {
        /** @var ReferenceBook|null $referenceBook */
        $referenceBook = $this->entityManager->getRepository(ReferenceBook::class)
            ->find(ReferenceBook::RB_ID_PACK_RB);
        if (!$referenceBook) {
            throw new NotFoundException("ReferenceBook " . ReferenceBook::RB_ID_PACK_RB . " not found", 404);
        }
        return $this->findByReferenceBook($referenceBook);
    }

    /**
     * @deprecated
     */
    public function findOneByValue(ReferenceBook $referenceBook, string $value, int $userId): ?ReferenceBookValue
    {
        return $this->repo->findOneBy([
            'referenceBook' => $referenceBook,
            'userId' => $userId,
            'value' => $value
        ]);
    }
}
