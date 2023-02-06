<?php

declare(strict_types=1);

namespace App\Repository\Moysklad;

use App\Domain\Entity\Moysklad\Contractor\MoyskladContractorStatus;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method flush()
 * @method MoyskladContractorStatus get($id)
 * @method MoyskladContractorStatus|null find($id)
 * @method MoyskladContractorStatus[] findByIds(array $ids)
 * @method persist(MoyskladContractorStatus $entity)
 * @method save(MoyskladContractorStatus $entity)
 * @method remove(MoyskladContractorStatus $entity)
 * @method delete(MoyskladContractorStatus $entity)
 *
 */
class MoyskladContractorRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository(\App\Domain\Entity\Moysklad\Contractor\MoyskladContractor::class);
    }


}
