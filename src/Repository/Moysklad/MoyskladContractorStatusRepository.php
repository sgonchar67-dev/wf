<?php

namespace App\Repository\Moysklad;

use App\Domain\Entity\Contractor\ContractorStatus;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

class MoyskladContractorStatusRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository(ContractorStatus::class);
    }
}