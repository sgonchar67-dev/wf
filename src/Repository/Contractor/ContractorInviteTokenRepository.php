<?php

namespace App\Repository\Contractor;

use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method \App\Domain\Entity\Contractor\ContractorInviteToken get($id)
 * @method ContractorInviteToken|null find($id)
 * @method remove(\App\Domain\Entity\Contractor\ContractorInviteToken $entity)
 * @method delete(ContractorInviteToken $entity)
 * @method persist(\App\Domain\Entity\Contractor\ContractorInviteToken $entity)
 * @method save(ContractorInviteToken $entity)
 */
class ContractorInviteTokenRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(ContractorInviteToken::class);
    }

    public function findOneByContractor(\App\Domain\Entity\Contractor\Contractor $contractor): ?ContractorInviteToken
    {
        return $this->repo->findOneBy(['contractor' => $contractor]);
    }
}