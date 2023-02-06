<?php

namespace App\Repository\Contractor;

use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Contractor\Contractor;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Contractor get($id)
 * @method Contractor|null find($id)
 * @method remove(\App\Domain\Entity\Contractor\Contractor $entity)
 * @method delete(Contractor $entity)
 * @method persist(Contractor $entity)
 * @method save(Contractor $entity)
 */
class ContractorRepository extends AbstractRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Contractor::class);
    }

    /**
     * @param Company $company
     * @param Company[]|int $contractorCompanies
     * @return array
     */
    public function findByContractorCompanies(Company $company, array $contractorCompanies): array
    {
        return $this->repo->findBy([
            'company' => $company,
            'contractorCompany' => $contractorCompanies
        ]);
    }

    public function findByContractorCompany(Company $company, Company $contractorCompany): ?Contractor
    {
        return $this->repo->findOneBy([
            'company' => $company,
            'contractorCompany' => $contractorCompany,
        ]);
    }
}