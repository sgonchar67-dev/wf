<?php

namespace App\Repository\Company;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Domain\Entity\Company\Company;
use App\Domain\Entity\Contractor\Contractor;
use App\Domain\Entity\Order\Order;
use App\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class CompanyRepository extends AbstractRepository
{
    public function __construct(
        private CompanyRepositoryAssistant $assistant,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
        $this->repo = $entityManager->getRepository(Company::class);
    }


    /**
     * @param Company $company
     * @param Company[] $excluded
     * @return Company[]
     */
    public function getSuppliers(Company $company, array $excluded = []): array
    {
        $companyIds = $this->assistant->getSupplierIds($company, $excluded);
        return $this->findByIds($companyIds);
    }

    /**
     * @param Company $company
     * @param Company[] $excluded
     * @return Company[]
     */
    public function getCustomers(Company $company, array $excluded = []): array
    {
        $companyIds = $this->assistant->getCustomersIds($company, $excluded);
        return $this->findByIds($companyIds);
    }

    /**
     * @param Company $company
     * @return Company[]
     */
    public function getContractorCompanies(Company $company): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('(c.contractorCompany) as companyId')
            ->distinct()
            ->from(Contractor::class, 'c')
            ->andWhere('c.contractorCompany is not null')
            ->andWhere('c.company = :company')
            ->setParameter('company', $company)
        ;
        $result = $qb->getQuery()->getResult();
        $companyIds = array_column($result, 'companyId');
        return $this->findByIds($companyIds);
    }
}